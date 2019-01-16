<?php
namespace DMore\ChromeDriver;

use Behat\Mink\Exception\DriverException;
use WebSocket\ConnectionException;

class ChromePage extends DevToolsConnection
{
    /** @var array */
    private $pending_requests = [];
    /** @var bool */
    private $page_ready = true;
    /** @var bool */
    private $has_javascript_dialog = false;
    /** @var array https://chromedevtools.github.io/devtools-protocol/tot/Network/#type-Response */
    private $response = null;

    public function connect($url = null)
    {
        parent::connect();
        $this->send('Page.enable');
        $this->send('DOM.enable');
        $this->send('Network.enable');
        $this->send('Animation.enable');
        $this->send('Animation.setPlaybackRate', ['playbackRate' => 100000]);
    }

    public function reset()
    {
        $this->response = null;
    }

    public function visit($url)
    {
        if (count($this->pending_requests) > 0) {
            $this->waitFor(function () {
                return count($this->pending_requests) == 0;
            });
        }
        $this->response = null;
        $this->page_ready = false;
        $this->send('Page.navigate', ['url' => $url]);
    }

    public function reload()
    {
        $this->page_ready = false;
        $this->send('Page.reload');
    }

    public function waitForLoad()
    {
        if (!$this->page_ready) {
            try {
                $this->waitFor(function () {
                    return $this->page_ready;
                });
            } catch (StreamReadException $exception) {
                if (!$exception->isEof() && $exception->isTimedOut()) {
                    $this->waitForLoad();
                }
            } catch (ConnectionException $exception) {
                throw new DriverException("Page not loaded");
            }
        }
    }

    public function getResponse()
    {
        $this->waitForHttpResponse();
        return $this->response;
    }

    /**
     * @return boolean
     */
    public function hasJavascriptDialog()
    {
        return $this->has_javascript_dialog;
    }

    public function getTabs()
    {
        $tabs = [];
        foreach ($this->send('Target.getTargets')['targetInfos'] as $tab) {
            if ($tab['type'] == 'page') {
                $tabs[] = $tab;
            }
        }
        return array_reverse($tabs, true);
    }

    private function waitForHttpResponse()
    {
        if (null === $this->response) {
            $parameters = ['expression' => 'document.readyState == "complete"'];
            $domReady = $this->send('Runtime.evaluate', $parameters)['result']['value'];
            if (count($this->pending_requests) == 0 && $domReady) {
                $this->response = [
                    'status' => 200,
                    'headers' => [],
                ];
                return;
            }

            $this->waitFor(function () {
                return null !== $this->response && count($this->pending_requests) == 0;
            });
        }
    }

    /**
     * @param array $data
     * @return bool
     * @throws DriverException
     */
    protected function processResponse(array $data)
    {
        if (array_key_exists('method', $data)) {
            switch ($data['method']) {
                case 'Page.javascriptDialogOpening':
                    $this->has_javascript_dialog = true;
                    return true;
                case 'Page.javascriptDialogClosed':
                    $this->has_javascript_dialog = false;
                    break;
                case 'Network.requestWillBeSent':
                    if ($data['params']['type'] == 'Document') {
                        $this->pending_requests[$data['params']['requestId']] = true;
                    }
                    break;
                case 'Network.responseReceived':
                    if ($data['params']['type'] == 'Document') {
                        unset($this->pending_requests[$data['params']['requestId']]);
                        $this->response = $data['params']['response'];
                    }
                    break;
                case 'Network.loadingFailed':
                    if ($data['params']['canceled']) {
                        unset($this->pending_requests[$data['params']['requestId']]);
                    }
                    break;
                case 'Page.frameNavigated':
                case 'Page.loadEventFired':
                case 'Page.frameStartedLoading':
                    $this->page_ready = false;
                    break;
                case 'Page.frameStoppedLoading':
                    $this->page_ready = true;
                    break;
                case 'Inspector.targetCrashed':
                    throw new DriverException('Browser crashed');
                    break;
                case 'Animation.animationStarted':
                    if (!empty($data['params']['source']['duration'])) {
                        usleep($data['params']['source']['duration'] * 10);
                    }
                    break;
                case 'Security.certificateError':
                    if (isset($data['params']['eventId'])) {
                        $this->send('Security.handleCertificateError', ['eventId' => $data['params']['eventId'], 'action' => 'continue']);
                        $this->page_ready = false;
                    }
                    break;
                default:
                    continue;
            }
        }

        return false;
    }
}

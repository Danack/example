echo "Now sending test message"

set -e

echo "Hello hello "
echo "Destination is ${TWILIO_DESTINATION} "

set +x

curl -X POST https://api.twilio.com/2010-04-01/Accounts/${TWILIO_SID}/Messages.json \
--data-urlencode "Body=This is a test message for repo ${GITHUB_REPOSITORY}, commit ${GITHUB_SHA}." \
--data-urlencode "From=${TWILIO_OA}" \
--data-urlencode "To=${TWILIO_DESTINATION}" \
-u ${TWILIO_SID}:${TWILIO_TOKEN}

echo "fin"
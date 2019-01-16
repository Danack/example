echo "Now sending test message"

set -e
set -x

echo "Hello hello "
echo "Destinatio is ${TWILIO_DESTINATION} "

curl -X POST https://api.twilio.com/2010-04-01/Accounts/${TWILIO_SID}/Messages.json \
--data-urlencode "Body=This is a test message." \
--data-urlencode "From=${TWILIO_OA}" \
--data-urlencode "To=${TWILIO_DESTINATION}" \
-u ${TWILIO_SID}:${TWILIO_TOKEN}



echo "fin"
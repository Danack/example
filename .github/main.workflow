workflow "Action testing" {
  resolves = [
    "Twilio Action",
  ]
  on = "push"
}

action "Twilio Action" {
  uses = "./docker/twilio"
  secrets = ["GITHUB_TOKEN", "TWILIO_SID", "TWILIO_TOKEN", "TWILIO_OA"]
}


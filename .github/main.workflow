workflow "Action testing" {
  resolves = [
    "Twilio Action",
  ]
  on = "push"
}

action "Twilio Action" {
  uses = "./"
  secrets = ["GITHUB_TOKEN", "TWILIO_SID", "TWILIO_TOKEN", "TWILIO_OA"]
}


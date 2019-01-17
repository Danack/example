workflow "Action testing" {
  resolves = [
    "Test Action",
  ]
  on = "push"
}

action "Test Action" {
  uses = "./docker/test"
  secrets = ["GITHUB_TOKEN"]
}


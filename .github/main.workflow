workflow "CI" {
  on = "push"
  resolves = [
    "PHPUnit Action",
  ]
}

action "PHPUnit Action" {
  uses = "linuxjuggler/phpunit-action@0.1"
}
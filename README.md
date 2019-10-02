# Github PR Review

Interface to simplify PR management on GitHub. 


## How to use

1. Create a Github's token ([here](https://github.com/settings/tokens)) and select `repo` scope
1. Create `.env.local` from `.env`
1. Fill `GITHUB_TOKEN` with your own token
1. Fill `GITHUB_LABELS_*` with you own labels for the differents categories 

![Liste des PR](github-pr-review.png)

### Configuration example

```
GITHUB_AUTH_METHOD=http_token
GITHUB_TOKEN=
GITHUB_REPOS='["username/repo_1", "username/repo_2", "username_2/repo_1"]'
GITHUB_LABELS_CHANGES_REQUESTED='["Changes requested"]'
GITHUB_LABELS_ACCEPTED='["Accepted"]'
GITHUB_LABELS_WIP='["WIP", "Pending answer"]'
GITHUB_BRANCHS_COLORS='[{"master": "warning"}, {"develop": "success"}, {"feature-*": "primary"}, {"release*": "info"}, , {"hotfix-*": "danger"}]'
GITHUB_BRANCH_DEFAULT_COLOR=danger

ENABLE_DARK_THEME=1
RELOAD_ON_FOCUS=0
# Reload time is ms
RELOAD_EVERY=60000
```

If you don't want to reload on focus and reload every x ms, you just need to set `RELOAD_ON_FOCUS=0` and `RELOAD_EVERY=0`.

## Installation

Install composer and assets:
```sh
make install
```

Start docker containers:
```sh
make start
```

## Check code quality

```sh
make check
```

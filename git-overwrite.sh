#!/bin/bash
git filter-branch --env-filter '
OLD_EMAIL="nhanthien.071972@gmail.com"
CORRECT_NAME="admin"
CORRECT_EMAIL="admin@kitchencorner.com"

export GIT_COMMITTER_NAME="$CORRECT_NAME"
export GIT_COMMITTER_EMAIL="$CORRECT_EMAIL"
export GIT_AUTHOR_NAME="$CORRECT_NAME"
export GIT_AUTHOR_EMAIL="$CORRECT_EMAIL"
' --tag-name-filter cat -- --branches --tags

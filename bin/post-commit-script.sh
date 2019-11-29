#!/usr/bin/env bash
# Adds the git-hook described below. Appends to the hook file
# if it already exists or creates the file if it does not.
# Note: CWD must be inside target repository

HOOK_DIR=$(git rev-parse --show-toplevel)/.git/hooks
HOOK_FILE="$HOOK_DIR"/post-commit

# Create script file if doesn't exist
if [ ! -e "$HOOK_FILE" ] ; then
        echo '#!/usr/bin/bash' > "$HOOK_FILE"
        chmod 700 "$HOOK_FILE"
fi

# Append hook code into script
cat > "$HOOK_FILE" <<EOF
#!/bin/sh

# destination of the final changelog file
OUTPUT_FILE=CHANGELOG.md

# generate the changelog
git --no-pager log --no-merges --format="%n #### %ai [%s](https://github.com/JMAConsulting/biz.jmaconsulting.oapproviderlistapp/commit/%h) commited by '%an (%cE)' <br>" > \$OUTPUT_FILE

# prevent recursion!
# since a 'commit --amend' will trigger the post-commit script again
# we have to check if the changelog file has changed or not
res=\$(git status --porcelain | grep \$OUTPUT_FILE | wc -l)
if [ "\$res" -gt 0 ]; then
  git add \$OUTPUT_FILE
  git commit --amend
  echo "Populated Changelog in \$OUTPUT_FILE"
fi

EOF

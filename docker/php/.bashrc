# PROMPT COLOURS
BLACK='\[\e[0;30m\]' # Black - Regular
RED='\[\e[0;31m\]' # Red - Regular
GREEN='\[\e[0;32m\]' # Green - Regular
YELLOW='\[\e[0;33m\]' # Yellow - Regular
BLUE='\[\e[0;34m\]' # Blue - Regular
PURPLE='\[\e[0;35m\]' # Purple - Regular
CYAN='\[\e[0;36m\]' # Cyan - Regular
WHITE='\[\e[0;37m\]' # White - Regular
BLACK_BOLD='\[\e[1;30m\]' # Black - Bold
RED_BOLD='\[\e[1;31m\]' # Red - Bold
GREEN_BOLD='\[\e[1;32m\]' # Green - Bold
YELLOW_BOLD='\[\e[1;33m\]' # Yellow - Bold
BLUE_BOLD='\[\e[1;34m\]' # Blue - Bold
PURPLE_BOLD='\[\e[1;35m\]' # Purple - Bold
CYAN_BOLD='\[\e[1;36m\]' # Cyan - Bold
WHITE_BOLD='\[\e[1;37m\]' # White - Bold
BLACK_FOREGROUND='\[\e[100m\]' # Black - Foreground
RED_FOREGROUND='\[\e[101m\]' # Red - Foreground
GREEN_FOREGROUND='\[\e[102m\]' # Green - Foreground
YELLOW_FOREGROUND='\[\e[103m\]' # Yellow - Foreground
BLUE_FOREGROUND='\[\e[104m\]' # Blue - Foreground
PURPLE_FOREGROUND='\[\e[105m\]' # Purple - Foreground
CYAN_FOREGROUND='\[\e[106m\]' # Cyan - Foreground
WHITE_FOREGROUND='\[\e[107m\]' # White - Foreground
RESET='\[\e[0m\]' # Text Reset

# alias
alias ls='ls --color=auto'
alias ll='ls -lah'
alias grep='grep --color=auto'

# history
HISTCONTROL=ignoreboth
shopt -s histappend
HISTSIZE=1000
HISTFILESIZE=2000

if [[ -f /etc/bash_completion ]]; then
    . /etc/bash_completion
fi

if [[ -f /etc/profile.d/bash_completion.sh ]]; then
    source /etc/profile.d/bash_completion.sh
fi

get_user() {
    if [[ $(id -u) -eq 0 ]]; then
        echo -n "${YELLOW_BOLD}\u"
    else
        echo -n "${RED_BOLD}\u"
    fi
}

get_host() {
    echo -n "${BLUE_BOLD}\h"
}

get_working_directory() {
    echo -n "${PURPLE_BOLD}\w"
}

get_branch() {
    local branch=$(git symbolic-ref HEAD --short 2> /dev/null)

    if [[ ! -z ${branch} ]]; then
        echo -n "(${branch})"
    fi
}

get_tag() {
    local tag=$(git describe --exact-match --tags HEAD 2> /dev/null)

    if [[ ! -z ${tag} ]]; then
        echo -n "(${tag})"
    fi
}

export PS1="$(get_user)${CYAN_BOLD}@$(get_host) $(get_working_directory) ${YELLOW_BOLD}\$(get_branch)${GREEN_BOLD}\$(get_tag) ${BLACK_BOLD}\n\$${RESET} "

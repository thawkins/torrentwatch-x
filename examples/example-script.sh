#!/bin/sh
# Example script that can be run from tw-x.
# You can configure your own script in the Configure dialog.
case $1 in
    favstart)
        curl -f -s -S -u <user>:<pass> --data-urlencode "text=TorrentWatch-X started downloading $2" --data-urlencode "user=< recipient >" http://twitter.com/direct_messages/new.xml
        ;;
    nonfavstart)
        echo "Somebody is downloading $2" | wall
        ;;
    error)
        echo "TorrentWatch-X encountered and error with $2 \n\n $3" | wall  
        ;;
esac


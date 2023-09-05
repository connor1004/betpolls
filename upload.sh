rsync -a -v -e "ssh -p18765" --exclude=node_modules --exclude=storage/logs /Users/softblade/work/php/betpolls/** softblad@77.104.135.77:/home/softblad/public_html/betpolls/
# ssh softblad@77.104.135.77 -p 18765 << EOF
# cd /home/softblad/public_html/betpolls;
# php71 artisan migrate:refresh;
# EOF
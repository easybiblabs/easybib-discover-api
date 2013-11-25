# this is a deploy hook executed by AWS OpsWorks
require 'rubygems'

# this requires our rubygem:
# https://rubygems.org/gems/BibOpsworks
require 'bib/opsworks'

# copy previous vendors in place when available
deploy_user = node["opsworks"]["deploy_user"]
BibOpsworks.new.copy_composer(release_path, deploy_user)

# `composer install` on top of it (in case of lock changes)
composer_command = "/usr/local/bin/php"
composer_command << " #{release_path}/composer.phar"
composer_command << " --no-dev"
composer_command << " --prefer-source"
composer_command << " --optimize-autoloader"
composer_command << " install"

run "cd #{release_path} && #{composer_command}"

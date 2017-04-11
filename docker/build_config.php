<?php

    // POST BOOT Build Config for:
    // easybib-discover-api

    function my_getenv($envvar, $default = false) {
        $data = trim(getenv($envvar));
        if(is_string($data) && $data!=='') return $data;
        else return $default;
    }

    function my_parse_url($data) {
        $url = parse_url($data);
        $url['path'] = ltrim( $url['path'], '/');
        return $url;
    }

    function envurltoarray($envvar) {
        $url = parse_url(getenv($envvar));
        $url['path'] = ltrim( $url['path'], '/');
        return $url;
    }

    function http_build_url($url) {
        $parsed_string = '';
        if (!empty($url['scheme'])) {
            $parsed_string .= $url['scheme'] . '://';
        }
        if (!empty($url['user'])) {
            $parsed_string .= $url['user'];
            if (isset($url['pass'])) {
                $parsed_string .= ':' . $url['pass'];
            }
            $parsed_string .= '@';
        }
        if (!empty($url['host'])) {
            $parsed_string .= $url['host'];
        }
        if (!empty($url['port'])) {
            $parsed_string .= ':' . $url['port'];
        }
        if (!empty($url['path'])) {
            $parsed_string .= '/' . $url['path'];
        }
        if (!empty($url['query'])) {
            $parsed_string .= '?' . $url['query'];
        }
        if (!empty($url['fragment'])) {
            $parsed_string .= '#' . $url['fragment'];
        }
        return $parsed_string;
    }

    //
    $ini = [];
    $ini['deployed_application']['appname'] = my_getenv('APPNAME','discover');
    $ini['deployed_application']['domains'] = my_getenv('DOMAINS','discover.data.easybib.bib');
    $ini['deployed_application']['deploy_dir'] = my_getenv('DEPLOY_DIR','/webroot/');
    $ini['deployed_application']['app_dir'] = my_getenv('APP_DIR','/webroot/');
    $ini['deployed_application']['doc_root_dir'] = my_getenv('DOC_ROOT_DIR','/webroot/www/');

    $ini['deployed_stack']['environment'] = my_getenv('STACK_ENVIRONMENT','vagrant');
    $ini['deployed_stack']['stackname'] = my_getenv('STACK_NAME','convox');

    $ini['settings']['QAFOO_PROFILER_KEY'] = my_getenv('QAFOO_PROFILER_KEY','GenericKey');
    $ini['settings']['OAUTH_URL_ID'] = my_getenv('OAUTH_URL_ID','http://id.easybib.bib:9900');
    $ini['settings']['OAUTH_URL_DATA'] = my_getenv('OAUTH_URL_DATA','http://data.easybib.bib');
    $ini['settings']['OAUTH_ID'] = my_getenv('OAUTH_ID','local-vagrant-api-id');
    $ini['settings']['OAUTH_SECRET'] = my_getenv('OAUTH_SECRET','local-vagrant-api-secret');

    $php_file = "<?php \nreturn " . var_export(json_decode(json_encode($ini), true), true) . ";\n";

    file_put_contents('/webroot/.deploy_configuration.php', $php_file);

    $environment = my_getenv('APP_ENVIRONMENT','DEVELOPMENT');
    if($environment == 'DEVELOPMENT') {
        print_r($php_file);
    }


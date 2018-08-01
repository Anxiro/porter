  php_cli_{{ $version->safe }}:
    build:
      context: .
      dockerfile: docker/php_cli_{{ $version->safe }}/Dockerfile
      cache_from:
        - konsulting/porter-php_cli_{{ $version->safe }}:latest
    image: konsulting/porter-php_cli_{{ $version->safe }}
    networks:
      - porter
    volumes:
      - {{ $home }}:/srv/app
      - ./storage/config/php_cli_{{ $version->safe }}/php.ini:/usr/local/etc/php/php.ini
      - ./storage/config/php_cli_{{ $version->safe }}/xdebug.ini:/usr/local/etc/php/conf.d/xdebug.ini
      - ./storage/config/php_cli_{{ $version->safe }}/bash_history:/root/.bash_history
    environment:
      - HOST_MACHINE_NAME={{ $host_machine_name }}
      - RUNNING_ON_PORTER=true

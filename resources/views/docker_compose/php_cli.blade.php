  php_cli_{{ $version->safe }}:
    build:
      context: .
      dockerfile: docker/php_cli_{{ $version->safe }}/Dockerfile
    image: konsulting/porter-php_cli_{{ $version->safe }}
    networks:
      - porter
    volumes:
      - {{ $home }}:/srv/app
      - ./storage/config/php_fpm_{{ $version->safe }}/php.ini:/usr/local/etc/php/php.ini
    environment:
       - DB_HOST={{ $db_host }}
       - RUNNING_ON_PORTER=true

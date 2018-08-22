  php_cli_{{ $version->safe }}:
    build:
      context: ./docker/{{ $imageSet }}
      dockerfile: php_cli_{{ $version->safe }}/Dockerfile
      cache_from:
        - {{ $imageSet }}-php_cli_{{ $version->safe }}:latest
    image: {{ $imageSet }}-php_cli_{{ $version->safe }}
    networks:
      - porter
    volumes:
      - {{ $home }}:/srv/app:delegated
      - ./storage/config/user/ssh:/root/.ssh
      - ./storage/config/{{ $version->cli_name }}/php.ini:/etc/php{{ $version->major }}/php.ini
      - ./storage/config/{{ $version->cli_name }}/xdebug.ini:/etc/php{{ $version->major }}/conf.d/xdebug.ini
      - ./storage/config/{{ $version->cli_name }}/bash_history:/root/.bash_history
    environment:
      - HOST_MACHINE_NAME={{ $host_machine_name }}
      - RUNNING_ON_PORTER=true

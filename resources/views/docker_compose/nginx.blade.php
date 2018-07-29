  nginx:
    build:
      context: .
      dockerfile: docker/nginx/Dockerfile
    image: konsulting/porter-nginx
    networks:
      - porter
    ports:
      - 80:80
      - 443:443
    volumes:
      - ./storage/config/nginx/nginx.conf:/etc/nginx/nginx.conf
      - ./storage/config/nginx/conf.d:/etc/nginx/conf.d
      - ./storage/ssl:/etc/ssl
      - ./storage/log:/var/log
      - {{ $home }}:/srv/app

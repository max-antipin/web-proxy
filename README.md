Run development stack on a local machine:
```Shell
docker compose up -d
```

Test stack:
```Shell
docker buildx bake --file .docker/bake.hcl test

docker compose -f .docker/compose.test.yaml --env-file .env up -d
```

```
docker image inspect max-antipin/web-proxy-dev-php:latest | php images.php
docker history --format=json --human=false --no-trunc max-antipin/web-proxy-dev-php:latest | php ./layers.php

docker buildx imagetools inspect localhost:5000/max-antipin/web-proxy-php-app

docker compose -f .docker/compose.prod.yaml --env-file .env up


docker compose -f compose.yaml -f compose.build.yaml build
docker compose -f ./.docker/compose.prod.yaml config
docker compose -f ./.docker/compose.prod.yaml up
docker compose -p txpg-advantshop -f compose.build.yaml

ln -s public public_html
```

WebProxy\Engine
WebProxy\Content abstract
WebProxy\Content\HtmlContent extends Content
WebProxy\ContentHandler abstract
WebProxy\ContentHandler\HtmlHandler extends ContentHandler
WebProxy\ContentHandler\RemoveScriptsHandler extends HtmlHandler
WebProxy\ContentHandler\UrlHandler extends HtmlHandler

WebProxy\ContentHandler abstract
WebProxy\ContentHandler\HtmlHandler extends ContentHandler
WebProxy\ContentHandler\Action abstract
WebProxy\ContentHandler\HtmlAction abstract extends Action
WebProxy\ContentHandler\RemoveScriptsAction extends HtmlAction
WebProxy\ContentHandler\UrlAction extends HtmlAction

WebProxy\ResponseHandler abstract
WebProxy\ResponseHandler\HtmlHandler extends ResponseHandler
WebProxy\ResponseHandler\Action abstract
WebProxy\ResponseHandler\HtmlAction abstract extends Action
WebProxy\ResponseHandler\RemoveScriptsAction extends HtmlAction
WebProxy\ResponseHandler\UrlAction extends HtmlAction

ContentHandler | ResponseHandler

WebProxy\Handler\ContentHandler
WebProxy\Handler\ContentHandlerMeta - ???
WebProxy\Handler\Content\HtmlHandler extends ContentHandler
WebProxy\Handler\Content\CssHandler extends ContentHandler

ResourceType
WebResource

Handler
Action|Step

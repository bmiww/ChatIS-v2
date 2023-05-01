<?php

parse_str($_SERVER['QUERY_STRING'], $query);

http_response_code(307);

if (isset($query['cmd'])) {
    header("Location: https://gist.github.com/IS2511/95ec4f720f6dbcebfe6142c751da6e39");
} else if (isset($query['mod'])) {
    header("Location: https://chatis.is2511.com/v2/control/mod-panel.php");
} else if (isset($query['funny-cmd'])) {
    header("Location: https://gist.github.com/IS2511/59a01251339352e518d14a606110003e");
} else {
    // Homepage
    header("Location: https://chatis.is2511.com/");
    die();
}

die();

<?php

$group_url = 'php';

$js = <<<EOF

<script type="text/javascript">

    console.log("js-$group_url");

</script>

EOF;
echo $js;

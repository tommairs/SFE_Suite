<?php

$pfile = "./previews/";
$pfile .= $_GET['content'];
$pfile .= ".html";

?>

<html>
<body>
<script>
    var windowWidth = 800;
    var windowHeight = 1000;
    var xPos = (screen.width/2) + (windowWidth/2);
    var yPos = (screen.height/2) - (windowHeight/2);
    window.open("<?php echo $pfile; ?>","POPUP","width=" 
    + windowWidth+",height="+windowHeight +",left="+xPos+",top="+yPos);
</script>
</body>
</html>

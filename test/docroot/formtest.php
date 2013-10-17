<html>
  <body>
    <form id="form1" action="#" method="post" enctype="multipart/form-data">
      <input id="file1" name="file1" type="file">
      <input type="submit" name="submit" value="ok">
    </form>
    <img id="preview">
    <pre><?php var_dump($_POST);?></pre>
  </body>
  <script type="text/javascript">
  f=document.getElementById('file1');
  p=document.getElementById('preview');
  f.onchange = function(){
    console.log(f.value);
  }
  </script>
</html>

<footer id="footer">
  Copyright <a href="http://takumidiary.com">tshop | takumidiary.com</a>
</footer>

<script src="js/vendor/jquery-2.2.2.min.js"></script>
<script>
    $(function(){
      var $ftr = $('#footer');
    if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
      $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
    }

    var $jsshow = $('#js-show-msg'),
        msg = $jsshow.text();
    if(msg.replace(/^[\s　]+|[\s　]+$/g,"").length){
      $jsshow.slideToggle('slow');
      setTimeout(function(){
        $jsshow.slideToggle('slow');
      },3000);
    }


    var $inputfile = $('.input-file,.prof-file-wrap'),
        $file = $('.file'),
        $culom = $('.culom');
    $inputfile.on('dragover',function(e){
      e.stopPropagation();
      e.preventDefault();
      $(this).css({
        border:'3px dashed #ccc'
      });
    });
    $inputfile.on('dragleave',function(e){
      e.stopPropagation();
      e.preventDefault();
      $(this).css({
        border:'none',
        fontSize:'0px'
      });
    });
    $file.on('change',function(e){
      $(this).closest('.input-file,.prof-file-wrap').css({
        border:'none',
        fontSize:'0px'
      });
      var fileimg = this.files['0'],
          $img = $(this).siblings('.prev-img'),
          fileReader = new FileReader();
      
       
      fileReader.onload = function(event){
        $img.attr('src',event.target.result).show();
      };

      fileReader.readAsDataURL(fileimg);
    });

    //お気に入り登録
    var $like,
        likeitemId;
    $like = $('.js-click-favo') || null;
    likeitemId = $like.data('itemid') || null;
    if(likeitemId !== undefined && likeitemId !== null){
      $like.on('click',function(){
        var $this = $(this);
        $.ajax({
          type:"POST",
          url:"ajaxFavo.php",
          data:{ itemId : likeitemId }
        }).done(function(data){
          console.log('Ajax Success');
          $this.find('.fas').toggleClass('favo-active');
        }).fail(function(msg){
          console.log('Ajax Error');
        });
      });
    }

    var $switchImgSubs = $('.item-sub-img'),
        $switchImgMain = $('#js-main-img');
    $switchImgSubs.on('click',function(e){
      $switchImgMain.attr('src',$(this).attr('src'));
    });

  });
</script>
</body>
</html>
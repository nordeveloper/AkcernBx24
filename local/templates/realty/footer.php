</div>
</div>
</div>
  
<?php if(!empty($_SESSION['notifymsg']) and !empty($_SESSION['notifytype']) ): ?>
    <script>
        let notifymsg ='<?php echo $_SESSION['notifymsg']?>';
        let notifytype = '<?php echo $_SESSION['notifytype']?>'; 
        ShowNotify(notifymsg, notifytype);
    </script>
    <?php unset($_SESSION['notifymsg'], $_SESSION['notifytype']) ?>
<?php endif?>

</body>
</html>
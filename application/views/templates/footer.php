
</div>

<?php if (isset($turnOff) && $turnOff != 0): ?>
    <script>
        $('.offline-ui-content').html('Online');
        var run = function () {
            Offline.options = {checks: {xhr: {url: '<?php echo base_url(); ?>assets/1px.png'}}};
            if (Offline.state === 'up') {
                Offline.check();
                $('.offline-ui-content').html('Online');
            } else {
                $('.offline-ui-content').html('Offline');
            }
        }
        setInterval(run, 10000);
    </script>
<?php endif; ?>

<?php if (!empty($show_map)): ?>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiA1bCNTFIEdXtyd0LWkR1ZtsserGkXIA&callback=<?php print $callback; ?>"></script>
<?php endif; ?>

</body>
</html>

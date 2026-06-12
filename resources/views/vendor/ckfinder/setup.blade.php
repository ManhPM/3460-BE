<script type="text/javascript" src="{{ asset('public/libs/ckfinder/ckfinder.js') }}"></script>
<script type="text/javascript" src="{{ asset('libs/ckeditor/config.js') }}"></script>
<script>
    CKFinder.config({
        connectorPath: @json(route('admin.ckfinder.connector'))
    });
    CKFinder.on('dialogOpened', function(evt) {
        if (evt.data.name === 'ImageCrop') {
            var cropPanel = evt.finder.request('panel:getActive');
            if (cropPanel) {
                var cropView = cropPanel.getChildViews().first();
                if (cropView && cropView.getChildViews && cropView.getChildViews().first) {
                    cropView = cropView.getChildViews().first();

                    // Đặt tỷ lệ mặc định là 1:1 (hình vuông)
                    setTimeout(function() {
                        // Tìm nút có data-value="1:1"
                        var squareButton = cropView.$el.find('[data-value="1:1"]');
                        if (squareButton.length) {
                            squareButton.click();
                        }
                    }, 100);
                }
            }
        }
    });
</script>

$(document).ready(function () {
    $('#myGallery').rtg({
        imageWidth:250,
    spacing:10,
    categories:true,
    categoryOptions: {
        defaultCategory: 'First',
    includeAll: false
    },
    captionOptions: {
                                        enableCaptions: false
                                    },
    lightbox:true,
    center: true,
    initialHeight: 500
    });
});

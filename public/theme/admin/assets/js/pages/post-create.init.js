var previewTemplate,
    dropzone,
    ckeditorClassic = document.querySelector("#ckeditor-classic"),
    dropzonePreviewNode =
        (ckeditorClassic &&
            ClassicEditor.create(document.querySelector("#ckeditor-classic"))
                .then(function (e) {
                    e.ui.view.editable.element.style.maxHeight = "600px";
                })
                .catch(function (e) {
                    console.error(e);
                }),
        ClassicEditor.create(document.querySelector("#ckeditor-classic-2"))
            .then((editor) => {
                editor.ui.view.editable.element.style.height = "50px";
            })
            .catch((error) => {
                console.error(error);
            }),
        ClassicEditor.create(document.querySelector("#ckeditor-classic-3"))
            .then((editor) => {
                editor.ui.view.editable.element.style.height = "200px";
            })
            .catch((error) => {
                console.error(error);
            }),
        ClassicEditor.create(document.querySelector("#ckeditor-classic-video"))
            .then((editor) => {
                editor.ui.view.editable.element.style.height = "200px";
            })
            .catch((error) => {
                console.error(error);
            }),
        document.querySelector("#dropzone-preview-list"));
dropzonePreviewNode &&
    ((dropzonePreviewNode.id = ""),
    (previewTemplate = dropzonePreviewNode.parentNode.innerHTML),
    dropzonePreviewNode.parentNode.removeChild(dropzonePreviewNode),
    (dropzone = new Dropzone(".dropzone", {
        url: "https://httpbin.org/post",
        method: "post",
        previewTemplate: previewTemplate,
        previewsContainer: "#dropzone-preview",
        maxFiles: 1,
        init: function () {
            this.on("addedfile", function (file) {
                if (this.files.length > 1) {
                    this.removeFile(this.files[0]);
                }
            });
        },
    })));

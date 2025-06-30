<!-- ✅ Use the prebuilt CKEditor build (CDN or local copy) -->
<script src="{{ asset('assets/ckeditor5_39.0.1_classic/ckeditor.js') }}"></script>
{{-- <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script> --}}
{{-- <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script> --}}

<script>
    class MyUploadAdapter {
        constructor(loader) {
            this.loader = loader;
        }

        upload() {
            return this.loader.file
                .then(file => new Promise((resolve, reject) => {
                    const data = new FormData();
                    data.append('upload', file);

                    fetch('/upload-image', {
                        method: 'POST',
                        body: data,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(result => {
                        resolve({ default: result.url });
                    })
                    .catch(error => {
                        reject(error);
                    });
                }));
        }

        abort() {}
    }

    function MyCustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new MyUploadAdapter(loader);
        };
    }

    window.initializeEditor = function(selector) {
        return ClassicEditor
            .create(document.querySelector(selector), {
                extraPlugins: [MyCustomUploadAdapterPlugin],
                toolbar: [
                    'undo', 'redo', '|',
                    'bold', 'italic', 'underline', '|',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                    'link', 'bulletedList', 'numberedList', '|',
                    'codeBlock', 'imageUpload'
                ]
            })
            .then(editor => {
                window.editor = editor;
                return editor;
            })
            .catch(error => {
                console.error(error);
            });
    };
</script>

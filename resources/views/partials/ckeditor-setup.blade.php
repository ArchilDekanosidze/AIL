<script src="{{ asset('assets/ckbox.js') }}"></script>

<script type="importmap">
    {
        "imports": {
            "ckeditor5": "{{ asset('assets/ckeditor5/ckeditor5.js') }}",
            "ckeditor5/": "{{ asset('assets/ckeditor5/') }}"
        }
    }
</script>

<script type="module">
    import {
        ClassicEditor,
        Essentials,
        Paragraph,
        Bold,
        Italic,
        Font,
        SimpleUploadAdapter,
        Image,
        ImageToolbar,
        ImageUpload
    } from 'ckeditor5';

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

        abort() {
            // Implement abort functionality if needed
        }
    }

    function MyCustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new MyUploadAdapter(loader);
        };
    }

    window.initializeEditor = function(selector) {
        return ClassicEditor
            .create(document.querySelector(selector), {
                licenseKey: 'GPL',
                plugins: [
                    Essentials, Paragraph, Bold, Italic, Font,
                    Image, ImageToolbar, ImageUpload,
                    SimpleUploadAdapter
                ],
                toolbar: [
                    'undo', 'redo', '|', 'bold', 'italic', '|',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'imageUpload'
                ],
                extraPlugins: [MyCustomUploadAdapterPlugin]
            })
            .then(editor => {
                window.editor = editor;  // Make the CKEditor instance globally accessible
                return editor;
            })
            .catch(error => {
                console.error(error);
            });
    };
</script>
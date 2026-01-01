<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Image Upload</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-6">🧪 Test Image Upload - Filesystem Structure</h1>

            <form id="uploadForm" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        User ID
                    </label>
                    <input
                        type="number"
                        name="user_id"
                        value="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">Will create folder: convene/<strong>user_id</strong>/discussion_id/timestamp</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Discussion ID
                    </label>
                    <input
                        type="number"
                        name="discussion_id"
                        value="5"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">Will create folder: convene/user_id/<strong>discussion_id</strong>/timestamp</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select Image
                    </label>
                    <input
                        type="file"
                        name="image"
                        accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">Max 5MB. Allowed: JPG, PNG, GIF, WebP</p>
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition"
                >
                    Test Upload Path
                </button>
            </form>

            <div id="result" class="mt-6 hidden">
                <h2 class="text-lg font-semibold mb-2">Result:</h2>
                <pre id="resultContent" class="bg-gray-900 text-green-400 p-4 rounded-md overflow-x-auto text-sm"></pre>
            </div>

            <div class="mt-6 p-4 bg-blue-50 rounded-md">
                <h3 class="font-semibold text-blue-900 mb-2">📁 Expected Folder Structure:</h3>
                <code class="text-sm text-blue-800">
                    storage/app/public/convene/{user_id}/{discussion_id}/{timestamp}/filename.jpg
                </code>
                <p class="text-xs text-blue-700 mt-2">
                    Example: storage/app/public/convene/1/5/20251231_143022/screenshot-a1b2c3d4.jpg
                </p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            const resultDiv = document.getElementById('result');
            const resultContent = document.getElementById('resultContent');

            try {
                const response = await fetch('/test-image-upload', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                resultContent.textContent = JSON.stringify(data, null, 2);
                resultDiv.classList.remove('hidden');

                if (data.status === 'success') {
                    resultContent.classList.remove('text-red-400');
                    resultContent.classList.add('text-green-400');
                } else {
                    resultContent.classList.remove('text-green-400');
                    resultContent.classList.add('text-red-400');
                }
            } catch (error) {
                resultContent.textContent = JSON.stringify({
                    status: 'error',
                    message: error.message
                }, null, 2);
                resultContent.classList.remove('text-green-400');
                resultContent.classList.add('text-red-400');
                resultDiv.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>

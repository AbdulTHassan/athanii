<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<!DOCTYPE html><html><body style='padding:40px;font-family:sans-serif'>";
    echo "<h1 style='color:green'>✅ POST REQUEST YA ISO!</h1>";
    echo "<h3>Abin da aka aika:</h3>";
    echo "<pre style='background:#f0f0f0;padding:20px;border-radius:8px'>";
    print_r($_POST);
    echo "</pre>";
    echo "<a href='test_edit_form.php'>← Koma form</a>";
    echo "</body></html>";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-4">Test Edit Form</h1>
        
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Title</label>
                <input type="text" name="title" value="Test Title" 
                       class="w-full px-3 py-2 border rounded">
            </div>
            
            <div>
                <label class="block text-sm font-semibold mb-1">Content</label>
                <textarea name="content" rows="5" 
                          class="w-full px-3 py-2 border rounded">Test content</textarea>
            </div>
            
            <button type="submit" 
                    class="w-full bg-emerald-700 text-white px-6 py-3 rounded font-semibold">
                Aika Form
            </button>
        </form>
    </div>
</body>
</html>
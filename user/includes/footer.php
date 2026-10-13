</main>

<footer class="bg-gray-900 text-white mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand -->
            <div>
                <h3 class="text-xl font-bold text-green-400">Zakat System</h3>
                <p class="mt-2 text-sm text-gray-400">
                    Helping Muslims fulfill their duty of Zakat with accurate calculations and Islamic guidance.
                </p>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="index.php" class="hover:text-green-400 transition">Home</a></li>
                    <li><a href="calculate.php" class="hover:text-green-400 transition">Calculate Zakat</a></li>
                    <li><a href="contact.php" class="hover:text-green-400 transition">Contact</a></li>
                </ul>
            </div>
            
            <!-- Resources -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Resources</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="#" class="hover:text-green-400 transition">Zakat Rules</a></li>
                    <li><a href="#" class="hover:text-green-400 transition">Nisab Calculator</a></li>
                    <li><a href="#" class="hover:text-green-400 transition">Fatwas</a></li>
                </ul>
            </div>
            
            <!-- Contact -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Contact</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li>📧 support@zakatsystem.com</li>
                    <li>📞 +1 234 567 8900</li>
                    <li>📍 Islamic Center, USA</li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-500">
            &copy; <?php echo date('Y'); ?> Zakat System. All rights reserved.
        </div>
    </div>
</footer>

<script>
// Slider functionality for index page
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    let currentSlide = 0;
    
    if (slides.length > 0) {
        // Function to show slide
        function showSlide(index) {
            // Hide all slides
            slides.forEach(slide => slide.classList.add('hidden'));
            // Show selected slide
            slides[index].classList.remove('hidden');
            
            // Update dots
            dots.forEach(dot => dot.classList.remove('active'));
            if (dots[index]) {
                dots[index].classList.add('active');
            }
        }
        
        // Auto-advance slides every 5 seconds
        setInterval(function() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }, 5000);
        
        // Add click event to dots
        dots.forEach((dot, index) => {
            dot.addEventListener('click', function() {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });
    }
});
</script>

</body>
</html>
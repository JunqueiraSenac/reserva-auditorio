<?php
// Define o caminho base dependendo de onde o arquivo está sendo chamado
$base_path = '';
if (strpos($_SERVER['PHP_SELF'], '/view/') !== false) {
    $base_path = '../';
}
?>

    <!-- Footer -->
    <footer class="bg-gray-900 dark:bg-black text-white py-12 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="flex items-center mb-6 md:mb-0">
                    <img src="<?php echo $base_path; ?>public/images/logo-senac.png" alt="SENAC Logo" class="h-12 w-auto mr-4">
                    <div>
                        <div class="text-lg font-bold">SENAC</div>
                        <div class="text-sm text-gray-400">Sistema de Reserva de Auditório</div>
                    </div>
                </div>

                <div class="text-center md:text-right">
                    <div class="text-sm text-gray-400 mb-1">
                        © <?php echo date('Y'); ?> SENAC. Todos os direitos reservados.
                    </div>
                    <div class="text-sm text-gray-500">
                        Desenvolvido com <i class="fas fa-heart text-red-500"></i> para educação
                    </div>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>

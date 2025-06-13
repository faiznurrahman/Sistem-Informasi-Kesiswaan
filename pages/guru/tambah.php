<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Registrasi Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'bounce-subtle': 'bounceSubtle 2s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        bounceSubtle: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-5px)' }
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-blue-900 dark:to-indigo-900 transition-all duration-500">

    <div class="container mx-auto px-4 py-8 animate-fade-in">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8 animate-slide-up">
                <h1 class="text-4xl font-bold text-black dark:text-white mb-2">
                    Form Registrasi Guru
                </h1>
                <p class="text-gray-600 dark:text-gray-300 text-lg">
                    Lengkapi data diri Anda dengan teliti
                </p>
                <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-purple-500 mx-auto mt-4 rounded-full"></div>
            </div>

            <!-- Form Container -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-3xl shadow-2xl p-8 animate-slide-up border border-white/20">
                <form id="teacherForm" class="space-y-8" action="pages/guru/simpan_guru.php" method="POST" enctype="multipart/form-data">
                    <!-- Personal Info Section -->
                    <div class="space-y-6">
                        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-bold">1</span>
                            </div>
                            Data Pribadi
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Lengkap -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Nama Lengkap *
                                </label>
                                <input 
                                    type="text" 
                                    id="namaLengkap"
                                    name="namaLengkap"
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-500" 
                                    placeholder="Masukkan nama lengkap"
                                    readonly
                                >
                            </div>
                            
                            <!-- NIP/NIK -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    NIP *
                                </label>
                                <input 
                                    type="text" 
                                    id="nip"
                                    name="nip"
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-500" 
                                    placeholder="Masukkan NIP/NIK"
                                >
                                <div id="nip-error" class="text-sm text-red-600 dark:text-red-400 mt-2 hidden"></div>
                            </div>
                            
                            <!-- Jenis Kelamin -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Jenis Kelamin *
                                </label>
                                <select 
                                    name="jenis_kelamin"
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-500 cursor-pointer"
                                >
                                    <option value="">Pilih jenis kelamin</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            
                            <!-- Tempat, Tanggal Lahir -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Tempat, Tanggal Lahir *
                                </label>
                                <input 
                                    name="tempat_tanggal_lahir"
                                    type="text" 
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-500" 
                                    placeholder="Jakarta, 01 Januari 1990"
                                >
                            </div>
                        </div>
                        
                        <!-- Alamat -->
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Alamat Lengkap *
                            </label>
                            <textarea 
                                required
                                name="alamat"
                                rows="3"
                                class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-500 resize-none" 
                                placeholder="Masukkan alamat lengkap Anda"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Contact & Education Section -->
                    <div class="space-y-6">
                        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-bold">2</span>
                            </div>
                            Kontak & Pendidikan
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- No Telepon -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    No Telepon *
                                </label>
                                <input 
                                    type="tel" 
                                    name="no_hp"
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-500" 
                                    placeholder="08xxxxxxxxxx"
                                >
                            </div>
                            
                            <!-- Email -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Email *
                                </label>
                                <input 
                                    type="email" 
                                    name="email"
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-500" 
                                    placeholder="nama@email.com"
                                >
                            </div>
                            
                            <!-- Pendidikan Terakhir -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Pendidikan Terakhir *
                                </label>
                                <select 
                                    required
                                    name="pendidikan_terakhir"
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-500 cursor-pointer"
                                >
                                    <option value="">Pilih pendidikan terakhir</option>
                                    <option value="Diploma 3">Diploma 3</option>
                                    <option value="S1">Sarjana (S1)</option>
                                    <option value="S2">Magister (S2)</option>
                                    <option value="S3">Doktor (S3)</option>
                                </select>
                            </div>

                            <!-- Program Studi -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Program Studi *
                                </label>
                                <input 
                                    type="text" 
                                    name="program_studi"
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-500" 
                                    placeholder="Pendidikan Matematika, Bahasa Indonesia, dll."
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Photo Upload Section -->
                    <div class="space-y-6">
                        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-teal-500 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-bold">3</span>
                            </div>
                            Foto Profil
                        </h2>
                        
                        <div class="flex justify-center">
                            <div class="w-full max-w-md">
                                <div
                                id="photo-dropzone"
                                class="relative border-[3px] border-dashed border-gray-300 dark:border-gray-600 rounded-2xl p-8 text-center cursor-pointer group hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-300 hover:bg-blue-50 dark:hover:bg-blue-900"
                                >
                                <input id="file-upload" name="file-upload" type="file" accept="image/*" class="sr-only" />

                                <div id="upload-content" class="space-y-4">
                                    <div
                                    class="mx-auto w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300"
                                    >
                                    <svg
                                        class="w-8 h-8 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        ></path>
                                    </svg>
                                    </div>

                                    <div>
                                    <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Upload Foto Guru</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Klik untuk pilih file atau drag & drop</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">PNG, JPG maksimal 10MB</p>
                                    </div>
                                </div>

                                <div id="preview-content" class="hidden">
                                    <img
                                    id="preview-image"
                                    class="mx-auto w-32 h-32 rounded-xl object-cover shadow-lg"
                                    alt="Preview"
                                    />
                                    <p class="mt-4 text-sm text-green-600 dark:text-green-400 font-medium">✓ Foto berhasil diupload</p>
                                    <button
                                    type="button"
                                    id="change-photo"
                                    class="mt-2 text-sm text-blue-600 dark:text-blue-400 hover:underline focus:outline-none"
                                    >
                                    Ganti foto
                                    </button>
                                </div>
                                </div>
                            </div>
                        </div>  
                        <div class="flex justify-center pt-6">
                            <button 
                                type="submit" 
                                class="
                                px-12 py-4 
                                bg-blue-700 
                                text-white 
                                font-bold rounded-2xl shadow-lg 
                                hover:bg-blue-800 
                                hover:shadow-xl transform hover:scale-105 
                                transition-all duration-300 
                                focus:outline-none focus:ring-4 
                                focus:ring-blue-300/50
                                "
                            >
                                <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Data
                                </span>
                            </button>
                        </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // File upload handling
        const fileInput = document.getElementById('file-upload');
        const dropzone = document.getElementById('photo-dropzone');
        const uploadContent = document.getElementById('upload-content');
        const previewContent = document.getElementById('preview-content');
        const previewImage = document.getElementById('preview-image');
        const changePhotoBtn = document.getElementById('change-photo');
        const nipInput = document.getElementById('nip');
        const namaInput = document.getElementById('namaLengkap');
        const nipError = document.getElementById('nip-error');

        // Click to upload
        dropzone.addEventListener('click', () => {
            if (!previewContent.classList.contains('hidden')) return;
            fileInput.click();
        });

        // Change photo button
        changePhotoBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.click();
        });

        // File input change
        fileInput.addEventListener('change', handleFileSelect);

        // Drag and drop
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('border-blue-400', 'bg-blue-50/50');
        });

        dropzone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-blue-400', 'bg-blue-50/50');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-blue-400', 'bg-blue-50/50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect();
            }
        });

        function handleFileSelect() {
            const file = fileInput.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImage.src = e.target.result;
                    uploadContent.classList.add('hidden');
                    previewContent.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        // NIP input handling for auto-filling name
        nipInput.addEventListener('blur', async () => {
            const nip = nipInput.value.trim();
            nipError.classList.add('hidden'); // Reset error message
            nipError.textContent = '';
            namaInput.value = '';
            namaInput.classList.remove('border-green-500', 'border-red-500');

            if (nip === '') {
                return;
            }

            try {
                const response = await fetch('pages/guru/get_nama.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ nip: nip }),
                });

                const data = await response.json();
                if (data.success && data.nama) {
                    namaInput.value = data.nama;
                    namaInput.classList.remove('border-red-500');
                    namaInput.classList.add('border-green-500');
                } else {
                    namaInput.classList.add('border-red-500');
                    nipError.textContent = 'NIP tidak ditemukan';
                    nipError.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error fetching name:', error);
                namaInput.classList.add('border-red-500');
                nipError.textContent = 'Terjadi kesalahan saat mencari nama. Silakan coba lagi.';
                nipError.classList.remove('hidden');
            }
        });

        
    </script>
</body>
</html>
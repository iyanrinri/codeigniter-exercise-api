<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Posts</title>
    <!-- Tailwind CSS from CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between">
                <div class="flex space-x-7">
                    <div>
                        <a href="#" class="flex items-center py-4 px-2">
                            <span class="font-semibold text-gray-500 text-lg">CI4 Blog</span>
                        </a>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-3">
                    <a href="#" class="py-2 px-2 font-medium text-gray-500 rounded hover:bg-blue-500 hover:text-white transition duration-300">Home</a>
                    <a href="#" class="py-2 px-2 font-medium text-gray-500 rounded hover:bg-blue-500 hover:text-white transition duration-300">About</a>
                    <a href="#" class="py-2 px-2 font-medium text-gray-500 rounded hover:bg-blue-500 hover:text-white transition duration-300">Contact</a>
                    <a id="loginBtn" href="#" class="py-2 px-2 font-medium text-white bg-blue-500 rounded hover:bg-blue-400 transition duration-300">Log In</a>
                </div>
                <div class="md:hidden flex items-center">
                    <button class="outline-none mobile-menu-button">
                        <svg class="w-6 h-6 text-gray-500 hover:text-blue-500" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Latest Blog Posts</h1>
            <button id="exportBtn" class="hidden bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded flex items-center transition-colors duration-300">
                <svg id="exportIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                <span id="exportText">Export to Excel</span>
                <svg id="exportSpinner" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </div>
        
        <div id="loading" class="text-center py-10">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
            <p class="mt-2 text-gray-500">Loading posts...</p>
        </div>

        <div id="error" class="hidden bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>Error loading posts. Please try again later or log in if you haven't already.</p>
        </div>

        <div id="login-prompt" class="hidden bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
            <p>You need to log in to view posts. <a href="#" id="promptLoginBtn" class="font-bold underline">Log in now</a></p>
        </div>
        
        <div id="exportSuccess" class="hidden bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>Excel file has been downloaded successfully!</p>
        </div>
        
        <div id="exportError" class="hidden bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>Failed to download Excel file. Please try again.</p>
        </div>
        
        <div id="posts-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Posts will be dynamically inserted here -->
        </div>
    </div>

    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Login</h3>
                <div class="mt-2 px-7 py-3">
                    <form id="loginForm" class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 text-left">Email</label>
                            <input type="email" id="email" name="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 text-left">Password</label>
                            <input type="password" id="password" name="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <p id="loginError" class="text-red-500 text-sm hidden">Invalid credentials. Please try again.</p>
                        <div class="flex justify-between">
                            <button type="button" id="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-white py-6 mt-12">
        <div class="max-w-6xl mx-auto px-4">
            <p class="text-center text-gray-500 text-sm">
                &copy; 2025 CI4 Blog. All rights reserved.
            </p>
        </div>
    </footer>

    <script>
        // Token management
        function getToken() {
            return localStorage.getItem('authToken');
        }

        function setToken(token) {
            localStorage.setItem('authToken', token);
        }

        function removeToken() {
            localStorage.removeItem('authToken');
        }

        // Toggle login status
        function updateLoginButton() {
            const loginBtn = document.getElementById('loginBtn');
            const exportBtn = document.getElementById('exportBtn');
            
            if (getToken()) {
                loginBtn.textContent = 'Logout';
                exportBtn.classList.remove('hidden');
            } else {
                loginBtn.textContent = 'Log In';
                exportBtn.classList.add('hidden');
            }
        }

        // Export posts to Excel using Fetch API with Blob handling
        async function exportToExcel() {
            const token = getToken();
            if (!token) return;
            
            // Show spinner and update button text
            document.getElementById('exportIcon').classList.add('hidden');
            document.getElementById('exportSpinner').classList.remove('hidden');
            document.getElementById('exportText').textContent = 'Downloading...';
            document.getElementById('exportBtn').disabled = true;
            document.getElementById('exportSuccess').classList.add('hidden');
            document.getElementById('exportError').classList.add('hidden');
            
            try {
                // Fetch the Excel file as a blob
                const response = await fetch('/posts/export', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Failed to download Excel file');
                }
                
                // Get the blob from the response
                const blob = await response.blob();
                
                // Create a temporary download link
                const downloadUrl = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = downloadUrl;
                
                // Get the filename from Content-Disposition or set a default
                const contentDisposition = response.headers.get('Content-Disposition');
                let filename = 'blog_posts_' + new Date().toISOString().split('T')[0] + '.xlsx';
                
                if (contentDisposition) {
                    const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                    if (filenameMatch && filenameMatch[1]) {
                        filename = filenameMatch[1].replace(/['"]/g, '');
                    }
                }
                
                link.download = filename;
                
                // Trigger the download
                document.body.appendChild(link);
                link.click();
                
                // Clean up
                window.URL.revokeObjectURL(downloadUrl);
                document.body.removeChild(link);
                
                // Show success message
                document.getElementById('exportSuccess').classList.remove('hidden');
                
                // Auto-hide the success message after 3 seconds
                setTimeout(() => {
                    document.getElementById('exportSuccess').classList.add('hidden');
                }, 3000);
            } catch (error) {
                console.error('Export error:', error);
                document.getElementById('exportError').classList.remove('hidden');
            } finally {
                // Reset button state
                document.getElementById('exportIcon').classList.remove('hidden');
                document.getElementById('exportSpinner').classList.add('hidden');
                document.getElementById('exportText').textContent = 'Export to Excel';
                document.getElementById('exportBtn').disabled = false;
            }
        }

        // Fetch posts from API
        async function fetchPosts() {
            const token = getToken();
            const loading = document.getElementById('loading');
            const error = document.getElementById('error');
            const loginPrompt = document.getElementById('login-prompt');
            const postsContainer = document.getElementById('posts-container');
            
            loading.classList.remove('hidden');
            error.classList.add('hidden');
            loginPrompt.classList.add('hidden');
            postsContainer.innerHTML = '';
            
            if (!token) {
                loading.classList.add('hidden');
                loginPrompt.classList.remove('hidden');
                return;
            }
            
            try {
                const response = await fetch('/posts', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.status === 401) {
                    // Unauthorized - token expired or invalid
                    removeToken();
                    updateLoginButton();
                    loading.classList.add('hidden');
                    loginPrompt.classList.remove('hidden');
                    return;
                }
                
                if (!response.ok) {
                    throw new Error('Failed to fetch posts');
                }
                
                const data = await response.json();
                
                if (data && data.data && Array.isArray(data.data)) {
                    loading.classList.add('hidden');
                    
                    if (data.data.length === 0) {
                        postsContainer.innerHTML = `
                            <div class="col-span-full text-center py-10">
                                <p class="text-gray-500">No posts found.</p>
                            </div>
                        `;
                        return;
                    }
                    
                    data.data.forEach(post => {
                        const date = new Date(post.created_at).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                        
                        const postElement = document.createElement('div');
                        postElement.className = 'bg-white rounded-lg shadow-md overflow-hidden';
                        postElement.innerHTML = `
                            <div class="p-6">
                                <h2 class="text-xl font-bold text-gray-800 mb-2">${post.title}</h2>
                                <p class="text-gray-600 text-sm mb-4">By ${post.username} on ${date}</p>
                                <p class="text-gray-600 mb-4">${post.content.substring(0, 150)}${post.content.length > 150 ? '...' : ''}</p>
                                <a href="/posts/${post.id}" class="text-blue-500 hover:text-blue-700 font-medium">Read more</a>
                            </div>
                        `;
                        postsContainer.appendChild(postElement);
                    });
                } else {
                    throw new Error('Invalid response format');
                }
            } catch (err) {
                console.error('Error fetching posts:', err);
                loading.classList.add('hidden');
                error.classList.remove('hidden');
            }
        }

        // Login functionality
        async function login(email, password) {
            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });
                
                const data = await response.json();
                
                if (response.ok && data.token) {
                    setToken(data.token);
                    updateLoginButton();
                    closeLoginModal();
                    fetchPosts();
                    return true;
                } else {
                    document.getElementById('loginError').classList.remove('hidden');
                    return false;
                }
            } catch (err) {
                console.error('Login error:', err);
                document.getElementById('loginError').classList.remove('hidden');
                return false;
            }
        }

        // Modal Functions
        function openLoginModal() {
            document.getElementById('loginModal').classList.remove('hidden');
            document.getElementById('loginError').classList.add('hidden');
            document.getElementById('email').value = '';
            document.getElementById('password').value = '';
        }

        function closeLoginModal() {
            document.getElementById('loginModal').classList.add('hidden');
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize
            updateLoginButton();
            fetchPosts();
            
            // Login/Logout Button
            document.getElementById('loginBtn').addEventListener('click', (e) => {
                e.preventDefault();
                if (getToken()) {
                    // Logout
                    removeToken();
                    updateLoginButton();
                    fetchPosts();
                } else {
                    // Open login modal
                    openLoginModal();
                }
            });
            
            // Export Button
            document.getElementById('exportBtn').addEventListener('click', (e) => {
                e.preventDefault();
                exportToExcel();
            });
            
            // Prompt Login Button
            document.getElementById('promptLoginBtn').addEventListener('click', (e) => {
                e.preventDefault();
                openLoginModal();
            });
            
            // Close Modal Button
            document.getElementById('closeModal').addEventListener('click', () => {
                closeLoginModal();
            });
            
            // Close Modal on Outside Click
            document.getElementById('loginModal').addEventListener('click', (e) => {
                if (e.target === document.getElementById('loginModal')) {
                    closeLoginModal();
                }
            });
            
            // Login Form Submission
            document.getElementById('loginForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                await login(email, password);
            });
        });
    </script>
</body>
</html>
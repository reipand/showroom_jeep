// Pastikan file ini berada di assets/js/
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('threejs-container');
    if (!container) return;

    // 1. Scene Setup
    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0x1a1a1a); // Warna gelap agar kontras dengan mobil

    // 2. Camera Setup
    const camera = new THREE.PerspectiveCamera(
        75, 
        container.clientWidth / container.clientHeight, 
        0.1, 
        1000
    );
    camera.position.set(2, 2, 4); 

    // 3. Renderer Setup
    const renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(container.clientWidth, container.clientHeight);
    container.appendChild(renderer.domElement);

    // 4. Controls (Untuk memutar model dengan mouse/touch)
    const controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true; 
    controls.target.set(0, 1, 0); 
    
    // 5. Lights
    const ambientLight = new THREE.AmbientLight(0xcccccc, 0.9);
    scene.add(ambientLight);

    const directionalLight = new THREE.DirectionalLight(0xffffff, 1.2);
    directionalLight.position.set(5, 5, 5).normalize();
    scene.add(directionalLight);

    // 6. GLTF Loader (Memuat Model 3D Jeep)
    const loader = new THREE.GLTFLoader();
    // PERHATIAN: Pastikan path ini benar!
    const modelPath = 'assets/models/wrangler.glb'; 
    
    loader.load(
        modelPath, 
        (gltf) => {
            scene.add(gltf.scene);
            // Sesuaikan skala agar pas di container
            gltf.scene.scale.set(1.5, 1.5, 1.5); 
        }, 
        undefined, 
        (error) => {
            console.error('Gagal memuat model 3D:', error);
            container.innerHTML = '<p style="color:white;text-align:center;padding-top:200px;">Gagal memuat model 3D. Pastikan file "wrangler.glb" ada di assets/models/.</p>';
        }
    );

    // 7. Render Loop
    function animate() {
        requestAnimationFrame(animate);
        controls.update(); 
        renderer.render(scene, camera);
    }
    animate();

    // Responsif
    window.addEventListener('resize', () => {
        camera.aspect = container.clientWidth / container.clientHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(container.clientWidth, container.clientHeight);
    });
});

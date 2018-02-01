<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Teste Three</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<link rel="stylesheet" href="dist/dropzone.css">
		<link rel="stylesheet" href="dist/basic.css">
		<link rel="stylesheet" href="dist/style.css">
		<link href='http://fonts.googleapis.com/css?family=Roboto:400,300,500,300italic|Inconsolata:400,700' rel='stylesheet' type='text/css'>
		<style>

			body {
				background-color: #e5e5e5;
				margin: 0px;
				overflow: hidden;
			}

		</style>
		<script src="dist/three/build/three.js"></script>
		<script src="dist/three/examples/js/loaders/STLLoader.js"></script>
		<script src="dist/three/examples/js/Detector.js"></script>
		<script src="dist/three/examples/js/controls/OrbitControls.js"></script>
		<script src="dist/three/examples/js/libs/jszip.min.js"></script>
		<script src="dist/dropzone.js"></script>
		<script src="dist/app.js"></script>
		<script src="dist/jquery-3.3.1.js"></script>
		<script src="dist/fastclick.js"></script>
	</head>
	<body>
		<script>

			if ( ! Detector.webgl ) Detector.addGetWebGLMessage();
			var camera, scene, renderer, grid, loader, controls, mesh, geometry, material;
			init();
			animate();

			Dropzone.options.myAwesomeDropzone = {
					url: "upload.php",
			    maxFilesize: 1000,
			    dictResponseError: 'Server not Configured',
					acceptedFiles: ".stl",
			    init:function(){
						this.on( "addedfile", function( file ){
							removeEntity( mesh );
							loadSTL('model/stl/'+file.name);
							this.emit( "thumbnail", file, 'model/thumbnail/stl.png' );
						}	)
			    }
		  };

			function init() {

				scene = new THREE.Scene();
				scene.background = new THREE.Color( 0xffffff );
				scene.add( new THREE.AmbientLight( 0x999999 ) );
				camera = new THREE.PerspectiveCamera( 35, window.innerWidth / ( window.innerHeight / 4 ), 1, 500 );
				camera.up.set( 0, 0, 1 );
				camera.position.set( 25, -50, 40);
				camera.lookAt(scene.position);
				camera.add( new THREE.PointLight( 0xffffff, 0.8 ) );
				scene.add( camera );
				grid = new THREE.GridHelper( 300, 8, 0x555555, 0xbab8b8 );
				grid.rotateOnAxis( new THREE.Vector3( 1, 0, 0 ), 90 * ( Math.PI / 180 ) );
				loader = new THREE.STLLoader();
				loadSTL('');
				renderer = new THREE.WebGLRenderer( { antialias: true } );
				renderer.setPixelRatio( window.devicePixelRatio );
				renderer.setSize( window.innerWidth, window.innerHeight / 4 );
				document.body.appendChild( renderer.domElement );
				controls = new THREE.OrbitControls( camera, renderer.domElement );
				controls.autoRotate = true;
				controls.addEventListener( 'change', render );
				controls.target.set( 0, 1.2, 2 );
				controls.update();
				window.addEventListener( 'resize', onWindowResize, false );

			}

			function onWindowResize() {

				camera.aspect = window.innerWidth / ( window.innerHeight / 4 );
				camera.updateProjectionMatrix();
				renderer.setSize( window.innerWidth, window.innerHeight / 4 );
				render();

			}

			function loadSTL(name){

				var a = loader.load( name, function ( geo ) {
					geometry = geo;
					material = new THREE.MeshPhongMaterial( { color: 0xafafaf, specular: 0x111111, shininess: 200 } );
					mesh = new THREE.Mesh( geometry, material );
					mesh.name = 'sceneObj';
					var box = new THREE.Box3().setFromObject(mesh);
					var array = box.getSize().toArray();
					array.sort( function ( a, b ){ return b - a } );
					var percent = 30 / array[ 0 ];
					mesh.scale.set( percent, percent, percent);
					box = new THREE.Box3().setFromObject( mesh );
					geometry.center();
					var tempGeo = new THREE.Geometry().fromBufferGeometry( mesh.geometry );
					mesh.geometry = new THREE.BufferGeometry().fromGeometry(tempGeo);
					mesh.position.z = box.getSize().getComponent( 2 ) / 2;
					scene.add( grid );
					scene.add( mesh );
					render();

				} );

			}

			function animate() {

				requestAnimationFrame( animate );
				render();

			}

			function removeEntity(object) {
				if (object) scene.remove( object );
				if (geometry) geometry.dispose();
				if (material) material.dispose();
				renderer.renderLists.dispose();
			}

			function render() {

				if ( mesh ) mesh.rotation.z -= 0.005;
				if ( grid ) grid.rotation.y -= 0.005;
				renderer.render( scene, camera );

			}

		</script>
		<section>
  			<div id="dropzone">
				<form action="/" method="post" class="dropzone needsclick" id="my-awesome-dropzone">
					<div class="dz-message needsclick">
    					Drop files here or click to upload.
					</div>
				</form>
			</div>
		</section>
	</body>
</html>

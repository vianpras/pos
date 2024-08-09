 <script type="module"> 
  // Import the functions you need from the SDKs you need
  import { initializeApp } from "https://www.gstatic.com/firebasejs/9.6.11/firebase-app.js";
  import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.6.11/firebase-analytics.js";
  // TODO: Add SDKs for Firebase products that you want to use
  // https://firebase.google.com/docs/web/setup#available-libraries

  // Your web app's Firebase configuration
  // For Firebase JS SDK v7.20.0 and later, measurementId is optional
  const firebaseConfig = {
    apiKey: "AIzaSyCulO2IJ7xL3Rg3AWm29wQZZdGIre2q4fM",
    authDomain: "glo-pos.firebaseapp.com",
    projectId: "glo-pos",
    storageBucket: "glo-pos.appspot.com",
    messagingSenderId: "505224913463",
    appId: "1:505224913463:web:ff555ac651c28b644395b2",
    measurementId: "G-LFT7YFVV4B"
  };

  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const analytics = getAnalytics(app);
</script>
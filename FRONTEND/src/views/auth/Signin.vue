<script setup>
import axios from 'axios';
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const username = ref('');
const password = ref('');
const role = ref('user'); // Menambahkan opsi role
const error = ref('');
const route = useRouter();

const signin = async() => {
  try {
    let endpoint = role.value === 'admin' ? 'admin/signin' : 'auth/signin';

    const response = await axios.post(endpoint, {
      username: username.value,
      password: password.value,
    });

    localStorage.setItem('token', response.data.token);
    axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
    
    if (role.value === 'admin') {
      route.push('/admins'); 
    } else {
      route.push('/manage-games'); 
    }
  } catch (e) {
    error.value = e.response.data.message;
  }
}
</script>

<template>
  <div class="container">
    <div class="form-container">
      <h2>Signin</h2>
      <p class="errors">{{ error }}</p>
      <div class="form-input">
        <input type="text" placeholder="Username" v-model="username">
        <input type="password" placeholder="Password" v-model="password">

        <div class="role-selector">
          <label for="role">Role:</label>
          <select v-model="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        
        <div class="option">  
          <p>Belum punya akun</p> <RouterLink to="/signup">Signup</RouterLink>
        </div>
        <button @click="signin">Signin</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.container {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.form-container {
  width: 400px;
  height: 450px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background-color: rgb(60, 60, 239);
  border-radius: 10px;
}

.form-input {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: center;
}

.form-input input {
  width: 70%;
  height: 20px;
  margin: 10px;
  padding: 10px;
  border: none;
  border: 1px solid black;
  border-radius: 5px;
}

.form-input button {
  width: 100%;
  padding: 10px;
  border: none;
  border-radius: 5px;
  background-color: yellowgreen;
  cursor: pointer;
}

.option {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 4px;
}

.role-selector {
  margin: 10px 0;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.role-selector select {
  width: 70%;
  padding: 8px;
  border: 1px solid black;
  border-radius: 5px;
}
</style>

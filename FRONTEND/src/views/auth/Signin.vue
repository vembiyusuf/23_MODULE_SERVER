<script setup>
import axios from 'axios';
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const username = ref('');
const password = ref('');
const error = ref('');
const route = useRouter();

const signin = async() => {
  try {
    const response = await axios.post('auth/signin', {
      username: username.value,
      password: password.value,
    });

    localStorage.setItem('token', response.data.token);
    axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
    console.log(response.data.token)
    route.push('/admins/listusers')
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
        <input type="text" placeholder="Password" v-model="password">
        
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
  height: 400px;
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
</style>
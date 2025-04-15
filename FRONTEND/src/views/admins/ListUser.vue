<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';

const username = ref('');
const password = ref('');
const successMessage = ref('');
const errorMessage = ref('');
const users = ref([]);
const error = ref('');

const addUser = async () => {
  try {
    const response = await axios.post('/users', {
      username: username.value,
      password: password.value,
    });
    console.log("Berhasil menambahkan user")
    console.log(response.data);

    successMessage.value = 'User berhasil ditambahkan!';
    errorMessage.value = '';
    username.value = '';
    password.value = '';

    await fetchUser();

    console.log(response.data);
  } catch (err) {
    successMessage.value = '';
    errorMessage.value = 'Gagal menambahkan user.';
    console.error(err);
  }
};

const fetchUser = async () => {
  try {
    const response = await axios.get('/users');
    users.value = response.data.users;
    errorMessage.value = '';
    console.log('Menampilkan user',response.data.users);
    console.log(users.value);
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Gagal mengambil data users.';
  }
};

const deleteUser = async (id) => {
  if (!confirm('Yakin ingin menghapus user ini?')) return;
  try {
    await axios.delete(`/users/${id}`);
    successMessage.value = 'User berhasil dihapus!';
    errorMessage.value = '';

    await fetchUser();
  } catch (err) {
    errorMessage.value = 'Gagal menghapus user.';
    console.error(err);
  }
};

onMounted(fetchUser);
</script>

<template>
  <div class="container">
    <div class="navbar">
      <h1>Admin</h1>
      <div class="navbar-links">
        <router-link to="/admins/listusers">List Users</router-link>
        <router-link to="/admins/">List Admins</router-link>
        <router-link to="/">Logout</router-link>
      </div>
    </div>
    <div class="form-container">
      <h1>Manage User</h1>
      <p style="color: red;">{{ errorMessage }}</p>
      <p style="color: green;">{{ successMessage }}</p>

      <div class="form-input">
        <input type="text" v-model="username" placeholder="Username" />
        <input type="text" v-model="password" placeholder="Password" />
        <button @click="addUser">Add User</button>
      </div>
    </div>

    <div class="game-list">
      <h2>Daftar User</h2>
      <table border="1" cellpadding="10">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Last Login</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>{{ user.id }}</td>
            <td>{{ user.username }}</td>
            <td>{{ user.created_at }}</td>
            <td>
              <button @click="editUser(user.id)">Edit</button>
              <button @click="deleteUser(user.id)">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p class="errors" style="color: red;">{{ error }}</p>
    </div>
  </div>
</template>

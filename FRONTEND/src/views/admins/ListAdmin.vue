<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';

const username = ref('');
const password = ref('');
const successMessage = ref('');
const errorMessage = ref('');
const admins = ref([]);
const error = ref('');

const addadmin = async () => {
  try {
    const response = await axios.post('/admins', {
      username: username.value,
      password: password.value,
    });

    successMessage.value = 'User berhasil ditambahkan!';
    errorMessage.value = '';
    username.value = '';
    password.value = '';

    await fetchAdmin();

    console.log(response.data);
  } catch (err) {
    successMessage.value = '';
    errorMessage.value = 'Gagal menambahkan admin.';
    console.error(err);
  }
};

const fetchAdmin = async () => {
  try {
    const response = await axios.get('/admins');
    admins.value = response.data.admin;
    console.log(response.data);
    errorMessage.value = '';
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Gagal mengambil data admins.';
  }
};

const deleteUser = async (id) => {
  if (!confirm('Yakin ingin menghapus user ini?')) return;
  try {
    await axios.delete(`/admins/${id}`);
    successMessage.value = 'Admin berhasil dihapus!';
    errorMessage.value = '';

    await fetchUser();
  } catch (err) {
    errorMessage.value = 'Gagal menghapus admin.';
    console.error(err);
  }
};

onMounted(fetchAdmin);
</script>

<template>
  <div class="container">
    <div class="navbar">
      <h1>Admin</h1>
      <div class="navbar-links">
        <router-link to="/admins/listusers">List Users</router-link>
        <router-link to="/admins">List Admins</router-link>
        <router-link to="/">Logout</router-link>
      </div>
    </div>
    <div class="form-container">
      <h1>Manage Admin</h1>
      <p style="color: red;">{{ errorMessage }}</p>
      <p style="color: green;">{{ successMessage }}</p>

      <div class="form-input">
        <input type="text" v-model="username" placeholder="Username" />
        <input type="text" v-model="password" placeholder="Password" />
        <button @click="addAdmin">Add Admin</button>
      </div>
    </div>

    <div class="game-list">
      <h2>Daftar Admin</h2>
      <table border="1" cellpadding="10">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Last Login At</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="admin in admins" :key="admin.id">
            <td>{{ admin.id }}</td>
            <td>{{ admin.username }}</td>
            <td>{{ admin.created_at }}</td>
            <td>
              <button @click="deleteUser(user.id)">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p class="errors" style="color: red;">{{ error }}</p>
    </div>
  </div>
</template>

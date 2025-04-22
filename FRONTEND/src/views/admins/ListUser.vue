<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import navbarAdmin from '@/components/navbarAdmin.vue';

const username = ref('');
const password = ref('');
const successMessage = ref('');
const errorMessage = ref('');
const users = ref([]);
const error = ref('');
const editingUserId = ref(null);

const fetchUser = async () => {
  try {
    const response = await axios.get('/users');
    users.value = response.data.users;
    errorMessage.value = '';
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Gagal mengambil data users.';
  }
};

const saveUser = async () => {
  try {
    if (editingUserId.value) {
      await axios.put(`/users/${editingUserId.value}`, {
        username: username.value,
        password: password.value || undefined,
      });
      successMessage.value = 'User berhasil diperbarui!';
    } else {
      await axios.post('/users', {
        username: username.value,
        password: password.value,
      });
      successMessage.value = 'User berhasil ditambahkan!';
    }

    errorMessage.value = '';
    username.value = '';
    password.value = '';
    editingUserId.value = null;
    await fetchUser();
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Gagal menyimpan user.';
    successMessage.value = '';
  }
};

const editUser = (id) => {
  const user = users.value.find(u => u.id === id);
  if (user) {
    username.value = user.username;
    password.value = '';
    editingUserId.value = user.id;
  }
};

const cancelEdit = () => {
  username.value = '';
  password.value = '';
  editingUserId.value = null;
  successMessage.value = '';
  errorMessage.value = '';
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
  }
};

onMounted(fetchUser);
</script>

<template>
  <div class="container">
    <div class="navbar">
      <h1>Admin</h1>
      <navbarAdmin></navbarAdmin>
    </div>

    <div class="form-container">
      <h1>Manage User</h1>
      <p style="color: red;">{{ errorMessage }}</p>
      <p style="color: green;">{{ successMessage }}</p>

      <div class="form-input">
        <input type="text" v-model="username" placeholder="Username" />
        <input type="text" v-model="password" placeholder="Password (kosongkan jika tidak diubah)" />
        <button @click="saveUser">{{ editingUserId ? 'Update User' : 'Add User' }}</button>
        <button v-if="editingUserId" @click="cancelEdit" style="margin-left: 10px;">Cancel</button>
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

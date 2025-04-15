<script setup>
import { onMounted, ref } from 'vue';

const admins = ref([]);
const error = ref('');

const fetchAdmins = async() => {
  try {
    const response = await axios.get('/admins');
    admins.value = response.data;
  } catch (e) {
    error.value = e.response.data.message;
  }
}

onMounted(fetchAdmins);
</script>


<template>
  <div class="container">
    <div class="admin-container">
      <h1>Daftar Admin</h1>
    </div>
    <div class="admin-list">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Password</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="admin in admins" :key="admin.id">
            <td>{{ admin.id }}</td>
            <td>{{ admin.username }}</td>
            <td>{{ admin.password }}</td>
          </tr>
        </tbody>
      </table>
      <p class="errors">{{ error }}</p>
  </div>
</div>
</template>


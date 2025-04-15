<template>
  <div>
    <h2 class="text-xl font-bold mb-4">Daftar Pengguna</h2>
    <ul v-if="users.length">
      <li v-for="user in users" :key="user.id">
        <template >
          <strong>{{ user.username }}</strong><br />
          <small>Dibuat pada: {{ formatDate(user.created_at) }}</small><br />
          <button @click="editUser(user.id)" >
            Edit
          </button>
          <button @click="deleteUser(user.id)">
            Delete
          </button>
        </template>
      </li>
    </ul>
    <p v-else>Loading data pengguna...</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const users = ref([])
const userYangDiedit = ref(null)
const formEdit = ref({
  username: ''
})

const fetchUsers = async () => {
  try {
    const token = localStorage.getItem('token') // ambil token
    const response = await axios.get('/users', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
    users.value = response.data.content
  } catch (error) {
    console.error('Gagal fetch data pengguna:', error)
  }
}


const deleteUser = async (userId) => {
  try {
    await axios.delete(`/users/${userId}`)
    users.value = users.value.filter(user => user.id !== userId)
  } catch (error) {
    console.error('Gagal menghapus pengguna:', error)
  }
}

const formatDate = (tanggal) => {
  return new Date(tanggal).toLocaleString('id-ID')
}

onMounted(fetchUsers)
</script>


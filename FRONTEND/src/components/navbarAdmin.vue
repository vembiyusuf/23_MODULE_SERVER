<template>
  <nav class="navbar">
    <ul>
      <li><router-link to="/admins">List Admin</router-link></li>
      <li><router-link to="/admins/list-users">List User</router-link></li>
    </ul>
  </nav>
</template>

<script>
export default {
  name: 'NavbarAdmin'
}
</script>

<style scoped>
.navbar {
  background-color: #333;
  padding: 10px;
}

.navbar ul {
  display: flex;
  list-style: none;
}

.navbar li {
  margin-right: 20px;
}

.navbar a {
  color: white;
  text-decoration: none;
  font-weight: bold;
}

.navbar a:hover {
  color: #4CAF50;
}
</style>

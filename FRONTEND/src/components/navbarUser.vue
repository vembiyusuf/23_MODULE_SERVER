<template>
  <nav class="navbar">
    <ul>
      <li><router-link to="/discover-games">Discover Games</router-link></li>
      <li><router-link to="/manage-games">Manage Games</router-link></li>
      <li><router-link to="/user-profiles">User Profile</router-link></li>
    </ul>
  </nav>
</template>

<script>
export default {
  name: 'NavbarUser'
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

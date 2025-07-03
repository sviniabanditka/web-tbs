<template>
  <!-- Main info panel with tabs for statistics and validation -->
  <div class="info-panel">
    <div class="tabs">
      <button :class="{active: tab==='stats'}" @click="tab='stats'">Statistics</button>
      <button :class="{active: tab==='validation'}" @click="tab='validation'">Validation</button>
    </div>
    <div v-if="tab==='stats'">
      <div v-if="stats">
        <!-- Statistics sections -->
        <section>
          <h5>Basic Stats</h5>
          <ul>
            <li><strong>Total Cells:</strong> {{ stats.basic_stats.total_cells }}</li>
            <li><strong>Passable Cells:</strong> {{ stats.basic_stats.passable_cells }}</li>
            <li><strong>Bridges:</strong> {{ stats.basic_stats.bridges }}</li>
          </ul>
          <div class="subsection">
            <strong>Biomes:</strong>
            <ul>
              <li v-for="(count, biome) in stats.basic_stats.biomes" :key="biome">
                {{ biome }}: {{ count }}
              </li>
            </ul>
          </div>
          <div class="subsection">
            <strong>Resources:</strong>
            <ul>
              <li v-for="(count, resource) in stats.basic_stats.resources" :key="resource">
                {{ resource }}: {{ count }}
              </li>
            </ul>
          </div>
        </section>
        <section>
          <h5>Biome Analysis</h5>
          <table>
            <thead>
              <tr>
                <th>Biome</th>
                <th>Count</th>
                <th>%</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(data, biome) in stats.biome_analysis" :key="biome">
                <td>{{ biome }}</td>
                <td>{{ data.count }}</td>
                <td>{{ data.percentage }}%</td>
              </tr>
            </tbody>
          </table>
        </section>
        <section>
          <h5>Resource Analysis</h5>
          <ul>
            <li><strong>Total Resources:</strong> {{ stats.resource_analysis.total_resources }}</li>
            <li><strong>Resource Density:</strong> {{ stats.resource_analysis.resource_density }}%</li>
          </ul>
          <table>
            <thead>
              <tr>
                <th>Resource</th>
                <th>Count</th>
                <th>% of Total</th>
                <th>% of Resources</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(data, resource) in stats.resource_analysis.resources_breakdown" :key="resource">
                <td>{{ resource }}</td>
                <td>{{ data.count }}</td>
                <td>{{ data.percentage_of_total }}%</td>
                <td>{{ data.percentage_of_resources }}%</td>
              </tr>
            </tbody>
          </table>
        </section>
        <section>
          <h5>Connectivity</h5>
          <ul>
            <li><strong>Passable %:</strong> {{ stats.connectivity.passable_percentage }}%</li>
            <li><strong>Bridges Count:</strong> {{ stats.connectivity.bridges_count }}</li>
            <li><strong>Water Tiles:</strong> {{ stats.connectivity.water_tiles }}</li>
          </ul>
        </section>
      </div>
      <div v-else>
        <p>No stats available.</p>
      </div>
    </div>
    <div v-else-if="tab==='validation'">
      <div v-if="validation">
        <!-- Validation sections -->
        <section>
          <h5>Biomes</h5>
          <table>
            <thead>
              <tr>
                <th>Biome</th>
                <th>Expected %</th>
                <th>Actual %</th>
                <th>Deviation</th>
                <th>Within Tolerance</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(data, biome) in validation.biomes" :key="biome">
                <td>{{ biome }}</td>
                <td>{{ data.expected_percentage }}%</td>
                <td>{{ data.actual_percentage }}%</td>
                <td>{{ data.deviation }}%</td>
                <td :class="{ pass: data.within_tolerance, fail: !data.within_tolerance }">
                  {{ data.within_tolerance ? '✔️' : '❌' }}
                </td>
              </tr>
            </tbody>
          </table>
        </section>
        <section>
          <h5>Resources</h5>
          <table>
            <thead>
              <tr>
                <th>Resource</th>
                <th>Min</th>
                <th>Max</th>
                <th>Actual</th>
                <th>Within Limits</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(data, resource) in validation.resources" :key="resource">
                <td>{{ resource }}</td>
                <td>{{ data.min_limit }}</td>
                <td>{{ data.max_limit }}</td>
                <td>{{ data.actual_count }}</td>
                <td :class="{ pass: data.within_limits, fail: !data.within_limits }">
                  {{ data.within_limits ? '✔️' : '❌' }}
                </td>
              </tr>
            </tbody>
          </table>
        </section>
        <section class="overall">
          <strong>Overall Compliance:</strong>
          <span :class="{ pass: validation.overall_compliance, fail: !validation.overall_compliance }">
            {{ validation.overall_compliance ? '✔️ Map matches config' : '❌ Map does not match config' }}
          </span>
        </section>
      </div>
      <div v-else>
        <p>No validation data.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useMapStore } from '@/stores/map';
const mapStore = useMapStore();
const stats = computed(() => mapStore.stats);
const validation = computed(() => mapStore.validation);
const tab = ref('stats');
</script>

<style scoped>
.info-panel {
  position: absolute;
  top: 10px;
  right: 10px;
  z-index: 10;
  background: rgba(255, 255, 255, 0.97);
  padding: 15px;
  border-radius: 8px;
  width: 420px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  font-family: sans-serif;
  font-size: 14px;
  max-height: 90vh;
  overflow-y: auto;
}
.tabs {
  display: flex;
  gap: 10px;
  margin-bottom: 10px;
}
.tabs button {
  flex: 1;
  padding: 8px 0;
  border: none;
  background: #eee;
  border-radius: 5px 5px 0 0;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s;
}
.tabs button.active {
  background: #1976d2;
  color: #fff;
}
h4, h5 {
  margin-top: 0;
  margin-bottom: 10px;
}
ul {
  list-style: none;
  padding: 0;
  margin: 0 0 10px 0;
}
li {
  text-transform: capitalize;
  margin-bottom: 5px;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 10px;
}
th, td {
  border: 1px solid #ddd;
  padding: 4px 8px;
  text-align: center;
}
.pass {
  color: #388e3c;
  font-weight: bold;
}
.fail {
  color: #d32f2f;
  font-weight: bold;
}
.overall {
  margin-top: 15px;
  font-size: 16px;
}
.subsection {
  margin-bottom: 10px;
}
</style> 
<template>
  <!-- Main info panel with tabs for statistics and validation -->
  <div class="info-panel">
    <div class="tabs">
      <button :class="{active: tab==='basic'}" @click="tab='basic'">Basic</button>
      <button :class="{active: tab==='biomes'}" @click="tab='biomes'">Biomes</button>
      <button :class="{active: tab==='resources'}" @click="tab='resources'">Resources</button>
    </div>
    <div v-if="tab==='basic'">
      <section v-if="stats.basic_statistics">
        <h5>Basic Statistics</h5>
        <ul>
          <li><strong>Total Cells:</strong> {{ stats.basic_statistics.total_cells }}</li>
          <li><strong>Passable Cells:</strong> {{ stats.basic_statistics.passable_cells }}</li>
          <li><strong>Bridges:</strong> {{ stats.basic_statistics.bridges }}</li>
        </ul>
        <div class="subsection">
          <strong>Biomes:</strong>
          <ul>
            <li v-for="(count, biome) in stats.basic_statistics.biomes" :key="biome">
              {{ biome }}: {{ count }}
            </li>
          </ul>
        </div>
        <div class="subsection">
          <strong>Resources:</strong>
          <ul>
            <li v-for="(count, resource) in stats.basic_statistics.resources" :key="resource">
              {{ resource }}: {{ count }}
            </li>
          </ul>
        </div>
        <div class="subsection" v-if="stats.generation_summary">
          <strong>Generation Summary:</strong>
          <ul>
            <li><strong>Total Cells:</strong> {{ stats.generation_summary.total_cells }}</li>
            <li><strong>Biome Size Impact:</strong> {{ stats.generation_summary.biome_size_impact }}</li>
            <li><strong>Connectivity Status:</strong> {{ stats.generation_summary.connectivity_status }}</li>
          </ul>
        </div>
        <h5>Config Validation</h5>
        <div>
          <span :class="{ pass: stats.config_validation.valid, fail: !stats.config_validation.valid }">
            {{ stats.config_validation.valid ? '✔️ Valid' : '❌ Invalid' }}
          </span>
        </div>
        <div v-if="stats.config_validation.errors && stats.config_validation.errors.length">
          <h6>Errors:</h6>
          <ul>
            <li v-for="(err, idx) in stats.config_validation.errors" :key="'err'+idx" class="fail">{{ err }}</li>
          </ul>
        </div>
        <div v-if="stats.config_validation.warnings && stats.config_validation.warnings.length">
          <h6>Warnings:</h6>
          <ul>
            <li v-for="(warn, idx) in stats.config_validation.warnings" :key="'warn'+idx" class="warn">{{ warn }}</li>
          </ul>
        </div>
      </section>
    </div>
    <div v-else-if="tab==='biomes'">
      <section v-if="stats.biome_analysis">
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
    </div>
    <div v-else-if="tab==='resources'">
      <section v-if="stats.resource_analysis">
        <h5>Resource Distribution</h5>
        <table>
          <thead>
            <tr>
              <th>Resource</th>
              <th>Actual</th>
              <th>Expected</th>
              <th>Available Tiles</th>
              <th>Efficiency</th>
              <th>Scarcity</th>
              <th>Density Achieved</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(data, resource) in stats.resource_analysis.resource_distribution" :key="resource">
              <td>{{ resource }}</td>
              <td>{{ data.actual_count }}</td>
              <td>{{ data.expected_count }}</td>
              <td>{{ data.available_tiles }}</td>
              <td>{{ data.efficiency }}%</td>
              <td>{{ data.scarcity_setting }}</td>
              <td>{{ data.density_achieved }}%</td>
            </tr>
          </tbody>
        </table>
        <h5>Abundance Analysis</h5>
        <ul>
          <li><strong>Setting:</strong> {{ stats.resource_analysis.abundance_analysis.setting }}</li>
          <li><strong>Total Resources Placed:</strong> {{ stats.resource_analysis.abundance_analysis.total_resources_placed }}</li>
          <li><strong>Total Resources Expected:</strong> {{ stats.resource_analysis.abundance_analysis.total_resources_expected }}</li>
          <li><strong>Abundance Efficiency:</strong> {{ stats.resource_analysis.abundance_analysis.abundance_efficiency }}%</li>
          <li><strong>Overall Density:</strong> {{ stats.resource_analysis.abundance_analysis.overall_density }}%</li>
        </ul>
        <h5>Biome Availability</h5>
        <table>
          <thead>
            <tr>
              <th>Resource</th>
              <th>Allowed Biomes</th>
              <th>Biome Counts</th>
              <th>Total Suitable Tiles</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(data, resource) in stats.resource_analysis.biome_availability" :key="resource">
              <td>{{ resource }}</td>
              <td>{{ data.allowed_biomes.join(', ') }}</td>
              <td>
                <span v-for="(count, biome) in data.biome_counts" :key="biome">
                  {{ biome }}: {{ count }}<span v-if="Object.keys(data.biome_counts).length > 1">; </span>
                </span>
              </td>
              <td>{{ data.total_suitable_tiles }}</td>
            </tr>
          </tbody>
        </table>
        <div class="subsection">
          <strong>Overall Efficiency:</strong> {{ stats.resource_analysis.overall_efficiency }}%
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useMapStore } from '@/stores/map';
const mapStore = useMapStore();
const stats = computed(() => mapStore.stats);
const tab = ref('basic');
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
  width: 480px;
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
h6 {
  margin: 8px 0 4px 0;
  font-size: 13px;
  color: #444;
}
ul {
  list-style: none;
  padding: 0;
  margin: 0 0 10px 0;
}
li {
  text-transform: none;
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
.warn {
  color: #e67e22;
  font-weight: bold;
}
.rec-type {
  font-weight: bold;
  color: #1976d2;
}
.rec-resource {
  font-weight: bold;
  color: #388e3c;
  margin-right: 4px;
}
.overall {
  margin-top: 15px;
  font-size: 16px;
}
.subsection {
  margin-bottom: 10px;
}
</style> 
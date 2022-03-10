<template>
  <b-card :header="$t('Toplam Varlıklarım')" header-tag="header">
    <b-row class="text-center justify-content-center align-items-center">
      <b-col cols="12">
        <apexchart
          type="donut"
          :options="chartOptions"
          :series="series"
          style="text-align: left"
        ></apexchart>
      </b-col>
    </b-row>
  </b-card>
</template>

<script>
export default {
  name: "WalletStatus",
  props: ["totalMount"],
  data: function () {
    return {
      series: Object.values(this.totalMount.donut),
      chartOptions: {
        chart: {
          width: "100%",
          type: "donut",
          dropShadow: {
            enabled: true,
            color: "#111",
            top: -1,
            left: 3,
            blur: 3,
            opacity: 0.2,
          },
        },
        stroke: {
          width: 0,
        },
        plotOptions: {
          pie: {
            donut: {
              labels: {
                show: true,
                total: {
                  showAlways: true,
                  show: true,
                  label: "Toplam Varlık",
                },
              },
            },
          },
        },
        labels: Object.keys(this.totalMount.donut),
        dataLabels: {
          dropShadow: {
            blur: 3,
            opacity: 0.8,
          },
        },
        fill: {
          type: "pattern",
          opacity: 1,
          pattern: {
            enabled: true,
            style: [
              "verticalLines",
              "squares",
              "horizontalLines",
              "circles",
              "slantedLines",
            ],
          },
        },
        states: {
          hover: {
            filter: "none",
          },
        },
        theme: {
          palette: "palette2",
        },
        responsive: [
          {
            breakpoint: 480,
            options: {
              chart: {
                width: "100%",
              },
              legend: {
                position: "bottom",
              },
            },
          },
        ]
      },
    };
  },
};
</script>

<style scoped>
</style>

import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts;

import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        init() {
            const saved = localStorage.getItem('theme');
            const sys = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            this.theme = saved || sys;
            this.apply();
        },
        theme: 'light',
        toggle() {
            this.theme = this.theme === 'light' ? 'dark' : 'light';
            localStorage.setItem('theme', this.theme);
            this.apply();
        },
        apply() {
            const isDark = this.theme === 'dark';
            document.documentElement.classList.toggle('dark', isDark);
            document.body.classList.toggle('dark', isDark);
            document.body.classList.toggle('bg-gray-900', isDark);
        }
    });

    Alpine.store('sidebar', {
        isExpanded: window.innerWidth >= 1280,
        isMobileOpen: false,
        isHovered: false,
        toggleExpanded() {
            this.isExpanded = !this.isExpanded;
            this.isMobileOpen = false;
        },
        toggleMobileOpen() {
            this.isMobileOpen = !this.isMobileOpen;
        },
        setMobileOpen(v) {
            this.isMobileOpen = v;
        },
        setHovered(v) {
            if (window.innerWidth >= 1280 && !this.isExpanded) {
                this.isHovered = v;
            }
        }
    });
});

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    if (!document.querySelector('#salesChart')) return;

    const el = document.getElementById('dashboard-chart-data');
    const chartData = el ? JSON.parse(el.textContent) : null;
    if (!chartData) return;

    const isDark = document.documentElement.classList.contains('dark');
    const labelColor = isDark ? '#e5e7eb' : '#6b7280';
    const borderColor = isDark ? '#1f2937' : '#f3f4f6';

    new ApexCharts(document.querySelector('#salesChart'), {
        series: chartData.series,
        chart: {
            type: 'bar',
            height: 320,
            fontFamily: 'Outfit, sans-serif',
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '39%',
                borderRadius: 5,
                borderRadiusApplication: 'end'
            }
        },
        dataLabels: { enabled: false },
        stroke: { show: true, width: 4, colors: ['transparent'] },
        xaxis: {
            categories: chartData.categories,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { colors: labelColor } }
        },
        yaxis: {
            title: false,
            labels: { style: { colors: labelColor } }
        },
        legend: {
            show: true,
            position: 'top',
            horizontalAlign: 'left',
            fontFamily: 'Outfit',
            markers: { radius: 99 }
        },
        grid: {
            borderColor: borderColor,
            yaxis: { lines: { show: true } }
        },
        fill: { opacity: 1 },
        tooltip: {
            x: { show: false },
            y: {
                formatter(val) { return val; }
            }
        },
        colors: ['#465fff']
    }).render();
});

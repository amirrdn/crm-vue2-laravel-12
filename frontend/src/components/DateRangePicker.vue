<template>
    <el-date-picker
      v-model="dateRange"
      type="daterange"
      range-separator="To"
      start-placeholder="Start date"
      end-placeholder="End date"
      :shortcuts="dateShortcutsWithFormattedValues"
      :disabled-date="disabledDate"
      @change="handleDateChange"
      value-format="YYYY-MM-DD"
      format="DD/MM/YYYY"
      size="large"
      class="w-full"
      :clearable="true"
    />
  </template>
  
  <script>
  import moment from 'moment'
  
  export default {
    name: 'DateRangePicker',
    data() {
      return {
        dateRange: null
      }
    },
    computed: {
      dateShortcutsWithFormattedValues() {
        const shortcuts = [
          {
            text: 'Today',
            value: () => {
              const d = moment()
              return [d.format('YYYY-MM-DD'), d.format('YYYY-MM-DD')]
            }
          },
          {
            text: 'This Week',
            value: () => {
              const start = moment().startOf('week')
              const end = moment().endOf('week')
              return [start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD')]
            }
          },
          {
            text: 'This Month',
            value: () => {
              const start = moment().startOf('month')
              const end = moment().endOf('month')
              return [start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD')]
            }
          },
          {
            text: 'Last Month',
            value: () => {
              const start = moment().subtract(1, 'month').startOf('month')
              const end = moment().subtract(1, 'month').endOf('month')
              return [start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD')]
            }
          }
        ]
  
        return shortcuts.map(s => ({
          text: s.text,
          value: () => {
            const range = s.value()
            return this.isValidDateRange(range) ? range : null
          }
        }))
      }
    },
    methods: {
      isValidDateString(dateStr) {
        return moment(dateStr, 'YYYY-MM-DD', true).isValid()
      },
      isValidDateRange(range) {
        return (
          Array.isArray(range) &&
          range.length === 2 &&
          this.isValidDateString(range[0]) &&
          this.isValidDateString(range[1])
        )
      },
      setDateRange(type) {
        let start, end
        switch (type) {
          case 'today':
            start = moment()
            end = moment()
            break
          case 'week':
            start = moment().startOf('week')
            end = moment().endOf('week')
            break
          case 'month':
            start = moment().startOf('month')
            end = moment().endOf('month')
            break
          case 'lastMonth':
            start = moment().subtract(1, 'month').startOf('month')
            end = moment().subtract(1, 'month').endOf('month')
            break
          default:
            this.dateRange = null
            return
        }
  
        const range = [start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD')]
        this.dateRange = this.isValidDateRange(range) ? range : null
      },
      handleDateChange() {
        this.$emit('change', this.dateRange)
      },
      disabledDate(time) {
        return time.getTime() > Date.now()
      }
    },
    watch: {
      dateRange(newRange, oldRange) {
        if (
          this.isValidDateRange(newRange) ||
          newRange === null
        ) {
          if (JSON.stringify(newRange) !== JSON.stringify(oldRange)) {
            this.handleDateChange()
          }
        } else {
          console.warn('Invalid dateRange detected in watcher:', newRange)
          this.dateRange = null
        }
      }
    },
    created() {
      const { startDate, endDate } = this.$route.query
      if (this.isValidDateRange([startDate, endDate])) {
        this.dateRange = [startDate, endDate]
      } else {
        this.dateRange = null
        this.$router.replace({ query: {} }).catch(() => {})
      }
  
      if (!this.dateRange) {
        const start = moment().startOf('month').format('YYYY-MM-DD')
        const end = moment().endOf('month').format('YYYY-MM-DD')
        this.dateRange = [start, end]
      }
    }
  }
  </script>
  
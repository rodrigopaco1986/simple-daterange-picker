<template>
  <FilterContainer>
    <span>{{ filter.name }}</span>
    <template #filter>
        <input type="text" class="hidden">
        <input
          :id="id"
          class="w-full form-control form-control-sm form-input form-input-bordered bg-gray-100 text-sm px-1"
          type="text"
          :dusk="`${filter.name}-daterange-filter`"
          name="daterangefilter"
          autocomplete="off"
          :value="value"
          :placeholder="placeholder"
          @keydown="handleInput($event)"
          @paste.prevent
        />
    </template>
  </FilterContainer>
</template>

<script>
import debounce from 'lodash/debounce'

export default {
  emits: ['change'],

  props: {
    resourceName: {
      type: String,
      required: true,
    },
    filterKey: {
      type: String,
      required: true,
    },
    lens: String,
  },

  data: () => ({
    id: null,
    value: null,
    startDate: null,
    endDate: null,
    currentStartDate: null,
    currentEndDate: null,
    debouncedHandleChange: null,
  }),

  created() {
    this.debouncedHandleChange = debounce(() => this.handleChange(), 500)

    this.setCurrentFilterValue()

    this.parseDates()
  },

  mounted() {
    this.id = 'dateRangeCalendar_' + this.generateId()
    
    Nova.$on('filter-reset', this.setCurrentFilterValue)
    
    setTimeout(() => {
      this.initDateRange()
    }, 1);
  },

  beforeUnmount() {
    Nova.$off('filter-reset', this.setCurrentFilterValue)
  },

  watch: {
    value() {
      this.debouncedHandleChange()
    },
  },

  methods: {
    setCurrentFilterValue() {
      this.value = this.filter.currentValue
    },
    handleChange() {
      this.$store.commit(`${this.resourceName}/updateFilterState`, {
        filterClass: this.filterKey,
        value: this.currentStartDate.format('YYYY-MM-DD') + ' to ' + this.currentEndDate.format('YYYY-MM-DD'),
      })

      this.$emit('change')

      this.currentStartDate = null
      this.currentEndDate = null
    },
    handleInput(e) {
        return e.preventDefault();
    },
    initDateRange: function() {
      const idSelector = ('#' + this.id)
      const ref = this

      $(idSelector).daterangepicker({
        "startDate": ref.startDate,
			  "endDate": ref.endDate,
        "maxDate": moment(),
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          },
      }, function(start, end, label) {
        if (start && end) {
          ref.currentStartDate = start
          ref.currentEndDate = end
        }
      })
      .on('apply.daterangepicker', function(ev, picker) {
        if (ref.currentStartDate && ref.currentEndDate) {
          ref.value = ref.currentStartDate.format('MM/DD/YYYY') + ' to ' + ref.currentEndDate.format('MM/DD/YYYY')
        }
      })
    },
    generateId: function () {
      return Math.random().toString(36).substring(2) +
        (new Date()).getTime().toString(36);
    },
    parseDates: function() {
      const dateRange = this.filter.currentValue
      let startDate = moment()
      let endDate = moment()
      
      if (dateRange) {
        const parsedDateRange = dateRange.split(' to ')
        if (parsedDateRange.length == 2) {
          try {
            startDate = moment(parsedDateRange[0], "YYYY-MM-DD")
            endDate = moment(parsedDateRange[1], "YYYY-MM-DD")
          } catch(e){}
        }
      }
      this.startDate = startDate.format('MM/DD/YYYY')
      this.endDate = endDate.format('MM/DD/YYYY')

      this.currentStartDate = startDate
      this.currentEndDate = endDate
    }
  },

  computed: {
    filter() {
      return this.$store.getters[`${this.resourceName}/getFilter`](
        this.filterKey
      )
    },
  },
}
</script>

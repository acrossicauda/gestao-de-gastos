<template>
  <DayPilotNavigator id="nav" :config="navigatorConfig" />
</template>

<script>
import {DayPilot, DayPilotNavigator} from '@daypilot/daypilot-lite-vue'
import { onMounted, reactive, ref } from 'vue';

export default {
  name: 'DatePicker',
  data: function() {
    return {
      navigatorConfig: {
        showMonths: 1,
        skipMonths: 1,
        selectMode: "Day",
        startDate: getDateNow(),
        selectionDay: getDateNow(),
        onTimeRangeSelected: args => {
          console.log("new date", args.day);
        }
      },
    }
  },
  props: {
  },
  components: {
    DayPilotNavigator
  },
  methods: {
  }
}


function getDateNow() {
  let dateToString = d => `${d.getFullYear()}-${('00' + (d.getMonth() + 1)).slice(-2)}-${('00' + d.getDate()).slice(-2)}` 
  let date = new Date();

  return dateToString(date);
}


const config = reactive({
  startDate: getDateNow(),
  viewType: "Week",
  durationBarVisible: true,
  eventHeight: 30,
  onEventMoved: (args) => {
    const name = args.e.data.text;
    console.log(`Event ${name} moved to ${args.newStart}`);
  },
  onEventResized: (args) => {
    console.log("Event resized: " + args.e.text());
  },
  onEventClicked: async (args) => {
    const form = [
      {name: "Text", id: "text"}
    ];
    const modal = await DayPilot.Modal.form(form, args.e.data);
    if (modal.canceled) {
      return;
    }
    navigatorConfig.value.control.events.update(modal.result);
  },
  onTimeRangeSelected: async args => {
    const modal = await DayPilot.Modal.prompt("Create a new event:", "Event 1");
    const dp = args.control;
    dp.clearSelection();
    if (modal.canceled) { return; }
    dp.events.add({
      start: args.start,
      end: args.end,
      id: DayPilot.guid(),
      text: modal.result
    });

    console.log(args)
    console.log(dp.events)
  },
  onBeforeEventRender: args => {
    const color = colors[args.data.type] || "#3c78d8";
    args.data.backColor = color + "55";
    args.data.borderColor = color + "44";
    args.data.barColor = color + "66";

    args.data.areas = [
      {
        right: 4,
        top: 3,
        width: 24,
        height: 24,
        padding: 2,
        fontColor: "#444444",
        backColor: color + "55",
        symbol: "icons/daypilot.svg#threedots-v",
        action: "ContextMenu",
        style: "border-radius: 50%"
      }
    ];

    if (args.data.locked) {
      args.data.text += " (locked)";
      args.data.areas.push({
        right: 30,
        top: 3,
        width: 24,
        height: 24,
        padding: 4,
        fontColor: "#444444",
        backColor: color + "55",
        symbol: "icons/daypilot.svg#padlock",
        style: "border-radius: 50%",
        onClick: async args => {
          const modal = await DayPilot.Modal.confirm("Do you really want to unlock this event?");
          if (modal.canceled) {
            return;
          }
          toggleEventLock(args.source);
        }
      });

      args.data.moveDisabled = true;
      args.data.resizeDisabled = true;
      args.data.clickDisabled = true;
      args.data.deleteDisabled = true;
    }
  },
  contextMenu: new DayPilot.Menu({
    items: [
      {text: "Edit...", onClick: args => editEvent(args.source) },
      {text: "Delete", symbol: "icons/daypilot.svg#x-4", onClick: args => deleteEvent(args.source) },
      {text: "Lock", symbol: "icons/daypilot.svg#padlock", onClick: args => toggleEventLock(args.source) },
      {text: "-"},
      {text: "Duplicate", onClick: args => duplicateEvent(args.source) },
      {text: "Postpone", symbol: "icons/daypilot.svg#minichevron-right-4", onClick: args => postponeEvent(args.source) },
      {text: "-"},
      {text: "Type", items: [
          {text: "Event", icon: "icon icon-blue", onClick: args => updateEventType(args.source, "event")},
          {text: "Task", icon: "icon icon-green", onClick: args => updateEventType(args.source, "task")},
          {text: "Reminder", icon: "icon icon-yellow", onClick: args => updateEventType(args.source, "reminder")},
          {text: "Holiday", icon: "icon icon-red", onClick: args => updateEventType(args.source, "holiday")},
        ]
      }
    ],
    onShow: args => {
      const e = args.source;
      const locked = e.data.locked;

      // update the lock/unlock text
      args.menu.items[2].text = locked ? "Unlock" : "Lock";

      // disable actions for locked
      args.menu.items[0].disabled = locked;
      args.menu.items[1].disabled = locked;
      args.menu.items[5].disabled = locked;
      args.menu.items[7].disabled = locked;
    }
  })
});
const navigatorConfig = ref(null);



const loadEvents = () => {
  const events = [
      {
          id: 1,
          start: getDateNow()+"T00:00:00", //"2021-09-10T10:00:00",
          end: getDateNow()+"T00:00:00", //"2021-09-10T10:00:00",
          text: "Event Teste",
          backColor: "#6aa84f",
          borderColor: "#38761d",
      },
      {
          id: 2,
          start: "2023-02-28T13:00:00",
          end: "2025-02-28T16:00:00",
          text: "Event 2",
          backColor: "#f1c232",
          borderColor: "#bf9000",
      },
  ];
  //console.log("FOIII",events);
  config.events = events;
  
  // const dp = args.control;
  //   dp.clearSelection();
  //   if (modal.canceled) { return; }
  //   dp.events.add({
  //     start: args.start,
  //     end: args.end,
  //     id: DayPilot.guid(),
  //     text: modal.result
  //   });
  //updateEventType(args.source, "event")
  //monthRef.value.control.events.update({events});
};

onMounted(() => {
  loadEvents();
});
</script>



<style>

.month_default_event_inner {
  border-radius: 20px;
  padding-left: 35px;

  white-space: nowrap;
}

.month_default_event_bar_inner {
  width: 30px;
  border-radius: 20px;
}

.month_default_event svg:hover {
  color: #000000;
  cursor: pointer;
}

/* context menu icons */
.icon:before {
  position: absolute;
  left: 0px;
  margin-left: 8px;
  margin-top: 3px;
  width: 14px;
  height: 14px;
  content: '';
}

.icon-blue:before { background-color: #3c78d8; }
.icon-green:before { background-color: #6aa84f; }
.icon-yellow:before { background-color: #f1c232; }
.icon-red:before { background-color: #cc0000; }

</style>

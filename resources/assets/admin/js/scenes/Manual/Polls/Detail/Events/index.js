/* eslint-disable no-shadow */
import React, { Component } from 'react';
// import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import classnames from 'classnames';
import {
  Card
} from '@shopify/polaris';

import Api from '../../../../../apis/app';
import Item from './Item';
import Add from './Add';
import Header from './Header';

class Events extends Component {
  constructor(props) {
    super(props);

    this.handleAdd = this.handleAdd.bind(this);
    this.handleEdit = this.handleEdit.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.handleDragEnd = this.handleDragEnd.bind(this);
    this.handleListUpdate = this.handleListUpdate.bind(this);
  }

  async handleAdd(values, bags) {
    bags.setSubmitting(true);
    const {
      page, list
    } = this.props;
    values.page_id = page.id;
    values.category_id = page.category_id;
    const data = await Api.post(`admin/manual/events`, values);
    const { response, body } = data;
    switch (response.status) {
      case 422:
        bags.setErrors(body);
        break;
      case 406:
        bags.setStatus(body.error);
        break;
      case 200:
        bags.setStatus(null);
        bags.setValues({
          name: '',
          name_es: '',
          candidate1_name: '',
          candidate1_score: 0,
          candidate1_standing: '',
          candidate1_odds: 0,
          candidate2_name: '',
          candidate2_score: 0,
          candidate2_standing: '',
          candidate2_odds: 0,
          candidate1_spread: 0,
          candidate2_spread: 0,
          over_under: 0,
          over_under_score: 0,
          tie_odds: 0,
        });
        bags.setTouched({});
        list.push(body);
        this.handleListUpdate(list);
        break;
      default:
        break;
    }
    bags.setSubmitting(false);
  }

  async handleEdit(id, values, bags, component, index) {
    bags.setSubmitting(true);
    const data = await Api.put(`admin/manual/events/${id}`, values);
    const { response, body } = data;
    switch (response.status) {
      case 422:
        bags.setErrors(body);
        break;
      case 406:
        bags.setStatus(body.error);
        break;
      case 200:
        bags.setStatus(null);
        const list = this.props.list;
        list[index] = body;
        this.handleListUpdate(list);
        break;
      default:
        break;
    }
    component.toggleEditMode();
    bags.setSubmitting(false);
    return data;
  }

  async handleToggleActive(id) {
    const data = await Api.put(`admin/manual/events/${id}/toggle-active`);
    const { response } = data;
    switch (response.status) {
      case 422:
        break;
      case 406:
        break;
      case 200:
        break;
      default:
        break;
    }
    return data;
  }

  async handleDelete(id, component, index) {
    component.setDeleting(true);
    const data = await Api.delete(`admin/manual/events/${id}`);
    const { response } = data;
    switch (response.status) {
      case 422:
        break;
      case 406:
        break;
      case 200:
        const list = this.props.list;
        list.splice(index, 1);
        this.handleListUpdate(list);
        break;
      default:
        break;
    }
    return data;
  }

  async handleDragEnd({ destination, source, type }) {
    if (!destination) {
      return;
    }
    const { list } = this.props;
    const sourceItem = list[source.index];
    const destinationItem = list[destination.index];
    const [removed] = list.splice(source.index, 1);
    list.splice(destination.index, 0, removed);

    const {body} = await Api.put(`admin/manual/events/${sourceItem.display_order}/${destinationItem.display_order}`, {
      page_id: this.props.page.id
    });
    this.handleListUpdate(body);
  }

  handleListUpdate(list) {
    this.props.onUpdate(list.slice(0));
  }

  render() {
    const {
      list, page
    } = this.props;
    return (
      <Card title="Polls" sectioned>
        <DragDropContext onDragEnd={this.handleDragEnd}>
          <Droppable droppableId="droppable" type="QUESTIONS">
            {(provided, snapshot) => (
              <div
                ref={provided.innerRef}
                className={
                  classnames({
                    categories: true,
                    active: snapshot.isDraggingOver
                  })
                }
              >
                <Header />
                {list.map((data, index) => (
                  <Draggable draggableId={`${data.id}`} index={index} key={`${data.id}`}>
                    {(provided, snapshot) => (
                      <div
                        className={classnames({
                          active: snapshot.isDragging
                        })}
                        ref={provided.innerRef}
                        {...provided.draggableProps}
                        // {...provided.dragHandleProps}
                        style={
                          { ...provided.draggableProps.style }
                        }
                      >
                        <Item
                          dragHandleProps={provided.dragHandleProps}
                          data={data}
                          page={page}
                          index={index}
                          onEdit={this.handleEdit}
                          onDelete={this.handleDelete}
                        />
                      </div>
                    )}
                  </Draggable>
                ))}
                {provided.placeholder}
              </div>
            )}
          </Droppable>
        </DragDropContext>
        <div className="poll-add-section">
          <Add onAdd={this.handleAdd} page={page} />
        </div>
      </Card>
    );
  }
}

export default Events;

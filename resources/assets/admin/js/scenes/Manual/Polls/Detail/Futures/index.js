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

class Futures extends Component {
  constructor(props) {
    super(props);

    this.handleAdd = this.handleAdd.bind(this);
    this.handleEdit = this.handleEdit.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.handleDragEnd = this.handleDragEnd.bind(this);
    this.handleListUpdate = this.handleListUpdate.bind(this);
    this.handleAnswersUpdate = this.handleAnswersUpdate.bind(this);
  }

  async handleAdd(values, bags) {
    bags.setSubmitting(true);
    const {
      page, list
    } = this.props;
    values.page_id = page.id;
    const data = await Api.post(`admin/manual/futures`, values);
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
          name_es: ''
        });
        bags.setTouched({});
        list.push({
          poll: body,
          answers: []
        });
        this.handleListUpdate(list);
        break;
      default:
        break;
    }
    bags.setSubmitting(false);
  }

  async handleEdit(id, values, bags, component, index) {
    bags.setSubmitting(true);
    const data = await Api.put(`admin/manual/futures/${id}`, values);
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
        list[index].poll = body;
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
    const data = await Api.put(`admin/manual/futures/${id}/toggle-active`);
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
    const data = await Api.delete(`admin/manual/futures/${id}`);
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
    const {
      list
    } = this.props;

    if (type === "QUESTIONS") {
      const sourceItem = list[source.index];
      const destinationItem = list[destination.index];
      const [removed] = list.splice(source.index, 1);
      list.splice(destination.index, 0, removed);
  
      const {body} = await Api.put(`admin/manual/futures/${sourceItem.poll.display_order}/${destinationItem.poll.display_order}`, {
        page_id: this.props.page.id
      });
      this.handleListUpdate(body);
    } else {
      const answers = list[parseInt(type, 10)].answers;

      const sourceItem = answers[source.index];
      const destinationItem = answers[destination.index];
      const [removed] = answers.splice(source.index, 1);
      answers.splice(destination.index, 0, removed);
  
      const {body} = await Api.put(`admin/manual/future-answers/${sourceItem.display_order}/${destinationItem.display_order}`, {
        future_id: list[parseInt(type, 10)].poll.id
      });
      list[parseInt(type, 10)].answers = body;
      this.handleListUpdate(list);
    }
  }

  handleListUpdate(list) {
    this.props.onUpdate(list.slice(0));
  }

  handleAnswersUpdate(answers, index) {
    const {
      list
    } = this.props;
    list[index].answers = answers;
    this.handleListUpdate(list);
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
                  <Draggable draggableId={`${data.poll.id}`} index={index} key={`${data.poll.id}`}>
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
                          data={data.poll}
                          page={page}
                          answers={data.answers}
                          index={index}
                          onEdit={this.handleEdit}
                          onDelete={this.handleDelete}
                          onAnswersUpdate={this.handleAnswersUpdate}
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
          <Add onAdd={this.handleAdd} />
        </div>
      </Card>
    );
  }
}

export default Futures;

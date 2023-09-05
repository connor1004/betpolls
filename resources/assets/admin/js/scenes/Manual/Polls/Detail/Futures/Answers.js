/* eslint-disable no-shadow */
import React, { Component } from 'react';
import { Droppable, Draggable } from 'react-beautiful-dnd';
import classnames from 'classnames';

import Api from '../../../../../apis/app';
import AnswerItem from './AnswerItem';
import AnswerAdd from './AnswerAdd';
import AnswerHeader from './AnswerHeader';

class Answers extends Component {
  constructor(props) {
    super(props);

    this.handleAdd = this.handleAdd.bind(this);
    this.handleEdit = this.handleEdit.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.handleListUpdate = this.handleListUpdate.bind(this);
    this.handleClose = this.handleClose.bind(this);
  }

  async handleAdd(values, bags) {
    bags.setSubmitting(true);
    const {
      page, list, poll
    } = this.props;
    values.page_id = page.id;
    values.future_id = poll.id;
    values.category_id = page.category_id;
    const data = await Api.post(`admin/manual/future-answers`, values);
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
    const data = await Api.put(`admin/manual/future-answers/${id}`, values);
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

  async handleDelete(id, component, index) {
    component.setDeleting(true);
    const data = await Api.delete(`admin/manual/future-answers/${id}`);
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

  handleListUpdate(list) {
    this.props.onUpdate(list.slice(0));
  }

  handleClose() {
    this.props.onCloseAdd();
  }

  render() {
    const {
      list, hasAdd, page, poll, index
    } = this.props;
    return (
      <div className="future-answers">
        <Droppable droppableId={`answers-${poll.id}`} type={`${index}`}>
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
              <AnswerHeader />
              {list.map((data, index) => (
                <Draggable draggableId={`answer-${data.id}`} index={index} key={`${index}`}>
                  {(provided, snapshot) => (
                    <div
                      className={classnames({
                        active: snapshot.isDragging
                      })}
                      ref={provided.innerRef}
                      {...provided.draggableProps}
                      {...provided.dragHandleProps}
                      style={
                        { ...provided.draggableProps.style }
                      }
                    >
                      <AnswerItem
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
        {hasAdd &&
          <div className="poll-add-section">
            <AnswerAdd onAdd={this.handleAdd} onClose={this.handleClose} page={page} />
          </div>
        }
      </div>
    );
  }
}

export default Answers;

import React, { Component } from 'react';
import { Formik } from 'formik';
import * as Yup from 'yup';
import {
  Card, Stack, Checkbox, Icon,
  Form, FormLayout,
  InlineError, TextField,
  Button, ButtonGroup, ResourceList, SkeletonBodyText
} from '@shopify/polaris';

import Answers from './Answers';

class Item extends Component {
  constructor(props) {
    super(props);
    this.state = {
      isDeleting: false,
      isEditMode: false,
      isAdding: false,
      showDeleteConfirm: false
    };
    this.formikRef = null;
    this.handleEdit = this.handleEdit.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.handleOpenAnswerAdd = this.handleOpenAnswerAdd.bind(this);
    this.handleCloseAnswerAdd = this.handleCloseAnswerAdd.bind(this);
    this.toggleEditMode = this.toggleEditMode.bind(this);
    this.toggleDeleteConfirm = this.toggleDeleteConfirm.bind(this);
    this.handleAnswerListUpdate = this.handleAnswerListUpdate.bind(this);
  }

  setDeleting(isDeleting) {
    this.setState({
      isDeleting
    });
  }

  toggleEditMode() {
    this.setState({
      isEditMode: !this.state.isEditMode
    });
  }

  toggleDeleteConfirm() {
    this.setState({
      showDeleteConfirm: !this.state.showDeleteConfirm
    });
  }

  async handleEdit(values, bags) {
    const {
      onEdit, data, index
    } = this.props;
    await onEdit(data.id, values, bags, this, index);
  }

  async handleDelete() {
    const {
      onDelete, data, index
    } = this.props;
    await onDelete(data.id, this, index);
  }

  handleOpenAnswerAdd() {
    this.setState({
      isAdding: true
    });
  }

  handleCloseAnswerAdd() {
    this.setState({
      isAdding: false
    });
  }

  handleAnswerListUpdate(answers) {
    this.props.onAnswersUpdate(answers, this.props.index);
    this.setState({
      isAdding: false
    });
  }

  renderEditMode() {
    const {
      data
    } = this.props;
    return (
      <Formik
        ref={this.formikRef}
        initialValues={{
          name: data.name || '',
          name_es: data.name_es || '',
          published: data.published || 0,
        }}
        validationSchema={
          Yup.object().shape({
            name: Yup.string().required('Name is required!'),
            name_es: Yup.string().required('Name ES is required!')
          })
        }
        onSubmit={this.handleEdit}
        render={({
          values,
          errors,
          status,
          touched,
          setFieldValue,
          handleSubmit,
          handleBlur,
          isSubmitting
        }) => (
          <Card>
            <ResourceList.Item>
              <Form onSubmit={handleSubmit}>
                <FormLayout>
                  <FormLayout.Group>
                    {status && <InlineError message={status} />}
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <Checkbox
                      label="Published"
                      checked={values.published == 1}
                      onChange={(value) => {
                        setFieldValue('published', value === true ? 1 : 0);
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      label="Name"
                      placeholder="Name"
                      name="name"
                      value={values.name}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('name', value);
                      }}
                      error={touched.name && errors.name}
                    />
                    <TextField
                      label="Name ES"
                      placeholder="Name ES"
                      name="name_es"
                      value={values.name_es}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('name_es', value);
                      }}
                      error={touched.name_es && errors.name_es}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <ButtonGroup segmented>
                      <Button
                        loading={isSubmitting}
                        submit
                        primary
                        icon="checkmark"
                      />
                      <Button
                        onClick={this.toggleEditMode}
                        icon="cancelSmall"
                      />
                    </ButtonGroup>
                  </FormLayout.Group>
                </FormLayout>
              </Form>
            </ResourceList.Item>
          </Card>
        )}
      />
    );
  }

  renderViewMode() {
    const {
      data, answers, page, index
    } = this.props;
    const {
      showDeleteConfirm, isDeleting, isAdding
    } = this.state;

    return (
      <Card>
        <ResourceList.Item>
          {
            showDeleteConfirm ? (
              <Stack>
                <Stack.Item fill>
                  {`Do you want to delete ${data.name}?`}
                </Stack.Item>
                <Stack.Item>
                  <ButtonGroup segmented>
                    <Button
                      primary
                      loading={isDeleting}
                      onClick={this.handleDelete}
                    >
                      Yes
                    </Button>
                    <Button
                      disabled={isDeleting}
                      onClick={this.toggleDeleteConfirm}
                    >
                      No
                    </Button>
                  </ButtonGroup>
                </Stack.Item>
              </Stack>
            ) : (
              <div {...this.props.dragHandleProps}>
                <Stack>
                  <Stack.Item>
                    <div style={{ width: '60px' }}>
                      {data.id}
                    </div>
                  </Stack.Item>
                  <Stack.Item fill>
                    {data.name}
                  </Stack.Item>
                  <Stack.Item fill>
                    {data.name_es}
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '100px' }}>
                      {data.published ? <Icon source='checkmark' /> : ''}
                    </div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '120px' }}>
                      <ButtonGroup segmented>
                        <Button
                          primary
                          icon="products"
                          onClick={this.toggleEditMode}
                        />
                        <Button
                          icon="add"
                          primary
                          onClick={this.handleOpenAnswerAdd}
                        />
                        <Button
                          destructive
                          icon="delete"
                          onClick={this.toggleDeleteConfirm}
                        />
                      </ButtonGroup>
                    </div>
                  </Stack.Item>
                </Stack>
              </div>
            )
          }
          <Answers
            list={answers}
            poll={data}
            page={page}
            hasAdd={isAdding}
            index={index}
            onCloseAdd={this.handleCloseAnswerAdd}
            onUpdate={this.handleAnswerListUpdate}
          />
          {!isAdding &&
            <Stack>
              <Stack.Item fill>
              </Stack.Item>
              <Stack.Item>
                <div style={{ width: '100px', marginTop: '5px' }}>
                  <ButtonGroup segmented>
                    <Button
                      icon="add"
                      primary
                      onClick={this.handleOpenAnswerAdd}
                    > Add</Button>
                  </ButtonGroup>
                </div>
              </Stack.Item>
            </Stack>
          }
        </ResourceList.Item>
      </Card>
    );
  }

  render() {
    const {
      isLoading
    } = this.props;
    const {
      isEditMode
    } = this.state;
    if (isLoading) {
      return (
        <Card>
          <ResourceList.Item>
            <SkeletonBodyText />
          </ResourceList.Item>
        </Card>
      );
    }
    return isEditMode ? this.renderEditMode() : this.renderViewMode();
  }
}

Item.defaultProps = {
  isLoading: false,
  onEdit: () => {},
  onDelete: () => {}
  // onToggleActive: () => {}
};

export default Item;
